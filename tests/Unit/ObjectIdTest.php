<?php

use Carbon\Carbon;
use SlimeSystems\ObjectId;
use SlimeSystems\ObjectId\Exception\Invalid;

describe('ObjectId', function () {
    it('creates a new ObjectId when no raw data is provided', function () {
        $objectId = new ObjectId();
        expect($objectId)->toBeInstanceOf(ObjectId::class);
        expect(strlen($objectId->toBinary()))->toBe(12);
        expect(strlen($objectId->toString()))->toBe(24);
    });

    it('creates an ObjectId from raw data', function () {
        $rawId = random_bytes(12);
        $objectId = ObjectId::fromBinary($rawId);
        expect($objectId)->toBeInstanceOf(ObjectId::class);
        expect($objectId->toBinary())->toBe($rawId);
    });

    it('throws Invalid exception for invalid raw data', function () {
        foreach ([10, 14] as $length) {
            expect(fn() => ObjectId::fromBinary(random_bytes($length)))->toThrow(
                Invalid::class,
            );
        }
    });

    it('creates an ObjectId from string', function () {
        $rawId = random_bytes(12);
        $hexId = bin2hex($rawId);
        $objectId = ObjectId::fromString($hexId);
        expect($objectId)->toBeInstanceOf(ObjectId::class);
        expect($objectId->toBinary())->toBe($rawId);
    });

    it('throws Invalid exception in fromString for a non-hex string', function () {
        expect(fn() => ObjectId::fromString('ダメ'))->toThrow(
            Invalid::class,
        );
    });

    it('throws Invalid exception in fromString when the length is no good', function () {
        foreach ([10, 14] as $length) {
            expect(fn() => ObjectId::fromString(bin2hex(random_bytes($length))))->toThrow(
                Invalid::class,
            );
        }
    });

    it('creates an ObjectId using fromTime with an integer timestamp', function () {
        $time = Carbon::parse('2000-01-01T00:00:00Z');
        $objectId = ObjectId::fromTime($time->getTimestamp());
        expect($time->equalTo($objectId->toTime()))->toBeTrue();
        $newerObjectId =  ObjectId::fromTime($time->getTimestamp());
        expect($objectId->toString())->not->toBe($newerObjectId->toString());
    });

    it('creates a non-unique ObjectId using fromTime', function () {
        $time = Carbon::parse('2000-01-01T00:00:00Z');
        $objectId = ObjectId::fromTime($time, unique: false);
        expect($objectId)->toBeInstanceOf(ObjectId::class);
        expect($time->equalTo($objectId->toTime()))->toBeTrue();
        expect(substr($objectId->toString(), -16))->toBe(str_repeat('00', 8));
    });

    it('returns the 24-char hex string via __toString()', function () {
        $objectId = ObjectId::fromString(str_repeat('00', 12));
        expect((string)$objectId)->toBe(str_repeat('00', 12));
    });

    it('returns the correct inspection string via inspect()', function () {
        $hexId = str_repeat('12ef', 6);
        $objectId = ObjectId::fromString($hexId);
        $inspectionText = $objectId->inspect();
        expect($inspectionText)->toContain($hexId);
        expect(strlen($inspectionText))->toBeGreaterThan(strlen($hexId));
    });

    it('returns true when comparing an ObjectId to itself', function () {
        $objectId = new ObjectId;
        expect($objectId->equals($objectId))->toBeTrue();
    });

    it('returns true when comparing two ObjectId instances with the same raw data', function () {
        $hexId = str_repeat('12ef', 6);
        $id1 = ObjectId::fromString($hexId);
        $id2 = ObjectId::fromString($hexId);
        expect($id1->equals($id2))->toBeTrue();
    });

    it('returns false when comparing two ObjectId instances with different raw data', function () {
        $id1 = ObjectId::fromString(str_repeat('11', 12));
        $id2 = ObjectId::fromString(str_repeat('22', 12));
        expect($id1->equals($id2))->toBeFalse();
    });

    it('returns false when comparing an ObjectId to a non-ObjectId object', function () {
        $time = Carbon::parse('2000-01-01T00:00:00Z');
        $objectId = ObjectId::fromTime($time, unique: false);
        expect($objectId->equals($time))->toBeFalse();
    });

    it('returns 0 when comparing two equal ObjectIds (compareTo)', function () {
        $objectId = new ObjectId;
        expect($objectId->compareTo($objectId))->toBe(0);
    });

    it('returns -1 when this ObjectId is lexicographically smaller (compareTo)', function () {
        $id1 = ObjectId::fromString('100000000000000000000000');
        $id2 = ObjectId::fromString('200000000000000000000000');
        expect($id1->compareTo($id2))->toBe(-1);
    });

    it('returns 1 when this ObjectId is lexicographically larger (compareTo)', function () {
        $id1 = ObjectId::fromString('200000000000000000000000');
        $id2 = ObjectId::fromString('100000000000000000000000');
        expect($id1->compareTo($id2))->toBe(1);
    });

    it('returns the correct DateTime object for the generation time', function () {
        $id1 = new ObjectId;
        $id2 = new ObjectId;
        expect((new Carbon($id1->toTime()))->lessThanOrEqualTo($id2->toTime()))->toBeTrue();
    });
});
