<?php

namespace SlimeSystems;

use Carbon\Carbon;
use DateTime;
use Exception;
use SlimeSystems\ObjectId\Exception\Invalid;
use SlimeSystems\ObjectIdInternal\Generator;

/**
 * Represents BSON ObjectId data (12 bytes).
 *
 * @see https://bsonspec.org/#/specification
 */
class ObjectId
{
    /**
     * @var string The 12-byte raw BSON data for the ObjectId.
     */
    private string $rawData;

    /**
     * @var Generator A static generator instance for new IDs.
     */
    private static Generator $generator;

    /**
     * Initializes the static generator.
     */
    private static function generator(): Generator
    {
        return self::$generator ??= new Generator();
    }

    /**
     * Constructor.
     *
     * @param string|null $rawData Optional 12-byte raw data. If null, a new ID is generated.
     * @throws Invalid if $rawData is not 12 bytes.
     */
    public function __construct(?string $rawData = null)
    {
        if ($rawData !== null) {
            if (strlen($rawData) !== 12) {
                throw new Invalid("Raw data must be 12 bytes for ObjectId.");
            }
            $this->rawData = $rawData;
        } else {
            $this->rawData = self::generator()->nextObjectId();
        }
    }

    /**
     * Create a new object id from raw bytes.
     *
     * @param string $data The raw 12 bytes.
     * @return ObjectId
     * @throws Invalid if $rawData is not 12 bytes.
     */
    public static function fromData(string $data): ObjectId
    {
        return new self($data);
    }

    /**
     * Create a new object id from a 24-character hexadecimal string.
     *
     * @param string $string The 24-character hex string.
     * @return ObjectId
     * @throws Invalid If the provided string is invalid.
     */
    public static function fromString(string $string): ObjectId
    {
        if (!self::legal($string)) {
            throw new Invalid("'$string' is an invalid ObjectId.");
        }
        return self::fromData(hex2bin($string));
    }

    /**
     * Create a new object id from a time.
     *
     * @param int|DateTime $time The timestamp or DateTime object.
     * @param bool $unique
     * @return ObjectId The new object id.
     * @throws Invalid
     */
    public static function fromTime(int|DateTime $time, bool $unique = true): ObjectId
    {
        $timestamp = $time instanceof DateTime ? $time->getTimestamp() : (int)$time;

        if ($unique) {
            $data = self::generator()->nextObjectId($timestamp);
        } else {
            $data = pack('N', $timestamp) . "\x00\x00\x00\x00\x00\x00\x00\x00";
        }

        return self::fromData($data);
    }

    /**
     * Determine if the provided string is a legal 24-character hex object id.
     *
     * @param string $string The string to check.
     * @return bool
     */
    private static function legal(string $string): bool
    {
        return (bool)preg_match('/^[0-9a-f]{24}$/i', $string);
    }

    /**
     * Get the string representation (24-char hex).
     *
     * @return string The object id as a string.
     */
    public function toString(): string
    {
        return bin2hex($this->rawData);
    }

    /**
     * Alias for toString() for string casting and internal compatibility.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Check equality of the object id with another object.
     *
     * @param mixed $other The object to check against.
     * @return bool If the objects are equal.
     */
    public function equals($other): bool
    {
        if (!($other instanceof ObjectId)) {
            return false;
        }
        return $this->rawData === $other->rawData;
    }

    /**
     * Compare this object id with another object for use in sorting.
     *
     * @param ObjectId $other The object to compare to.
     * @return int -1, 0, or 1.
     */
    public function compareTo(ObjectId $other): int
    {
        // Comparing the raw 12-byte string is equivalent to BSON comparison.
        return $this->rawData <=> $other->rawData;
    }

    /**
     * Return the UTC time at which this ObjectId was generated.
     *
     * @return DateTime The time the id was generated.
     * @throws Exception
     */
    public function getGenerationTime(): DateTime
    {
        // Get the first 4 bytes (timestamp)
        $timestamp_bytes = substr($this->rawData, 0, 4);

        $timestamp = unpack('N', $timestamp_bytes)[1];

        // Create a DateTime object from the timestamp in UTC
        return Carbon::createFromTimestamp($timestamp);
    }

    /**
     * Get a nice string for use with object inspection.
     *
     * @return string The object id in form BSON\ObjectId('id')
     */
    public function inspect(): string
    {
        return "SlimeSystems\\ObjectId('{$this->toString()}')";
    }
}

// To use this class:
// try {
//     $objectId = new BSON\ObjectId();
//     echo $objectId->toString() . "\n";
//     echo $objectId->getGenerationTime()->format(DateTime::ATOM) . "\n";
// } catch (\Exception $e) {
//     echo "Error: " . $e->getMessage();
// }
