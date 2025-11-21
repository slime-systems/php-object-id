<?php

namespace SlimeSystems\ObjectIdInternal;

use function pack;
use function random_bytes;
use function random_int;
use function substr;
use function time;

/**
 * @internal The class that encapsulates the behaviour of actually generating each
 * part of the ObjectId.
 */
class Generator
{
    private const COUNTER_MAX = 0xFFFFFF;
    private const PID_LENGTH = 5;

    /**
     * @var string The unique identifier generated per process ID (PID).
     */
    private string $processId;

    /**
     * @var int The counter value.
     */
    private int $counter;

    /**
     * Instantiate the new object id generator.
     */
    public function __construct()
    {
        $this->processId = random_bytes(self::PID_LENGTH);
        $this->counter = random_int(0, self::COUNTER_MAX);
    }

    /**
     * Return object id data based on the current time, incrementing the counter.
     *
     * @param int|null $time The optional timestamp to generate with.
     * @return string The raw 12-byte object id.
     */
    public function nextObjectId(?int $time = null): string
    {
        $time ??= time();
        $count = $this->counter = $this->counter + 1 & self::COUNTER_MAX;

        return $this->generate($time, $count);
    }

    /**
     * Generate object id data for a given time using the provided counter.
     *
     * @param int $time The time since epoch in seconds. (4 bytes)
     * @param int $counter The 3-byte counter.
     * @return string The raw 12-byte object id.
     */
    public function generate(int $time, int $counter = 0): string
    {
        // BSON ObjectId structure:
        // 1. time (4 bytes, big-endian)
        // 3. processId (5 bytes)
        // 4. counter (3 bytes, big-endian)

        // PHP's pack function:
        // 'N': unsigned long (4 bytes, big-endian) for time
        // 'N': unsigned long (4 bytes, big-endian) for counter. We only use the last 3 bytes of the packed result.
        $timeBytes = pack('N', $time);                      // 4 bytes: Time
        $counter_bytes = substr(pack('N', $counter), 1, 3); // 4 bytes: Counter (only the last 3 are used)

        // Concatenate the pieces:
        // Time (4) | PID (5) | Counter (3)
        return "{$timeBytes}{$this->processId}{$counter_bytes}";
    }
}
