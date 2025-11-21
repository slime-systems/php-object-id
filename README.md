# Object ID

A feature-packed, standalone **ObjectId** implementation for PHP.

[![Latest Version](https://img.shields.io/packagist/v/slime-systems/object-id)](https://packagist.org/packages/slime-systems/object-id)
[![Tests](https://github.com/slime-systems/php-object-id/actions/workflows/php.yml/badge.svg)](https://github.com/slime-systems/php-object-id/actions/workflows/php.yml)

---

**SlimeSystems\ObjectId** provides a BSON-compatible identifier generator and utility class, fully compliant with the [MongoDB ObjectId specification](https://www.mongodb.com/docs/manual/reference/bson-types/#objectid).

It is lightweight, requires no external extensions (like `mongodb`), and is perfect for generating sortable, unique identifiers in any PHP application.

## 🚀 Installation

Install via Composer:

```bash
composer require slime-systems/object-id
```

## ⚡ Quick Start

```php
use SlimeSystems\ObjectId;

// Generate a new unique ID
$id = new ObjectId();

echo $id; 
// Output: 507f1f77bcf86cd799439011

// Get the generation time
$timestamp = $id->toTime(); 
// Returns a DateTime object
```

## 📖 Usage Guide

### Creating ObjectIds

#### Generate a New ID
Create a fresh, unique 12-byte identifier.

```php
$id = new ObjectId;
```

#### From Hex String
Restore an ObjectId from its 24-character hexadecimal representation.

```php
$id = ObjectId::fromString('507f1f77bcf86cd799439011');
```

#### From Binary Data
Create from a raw 12-byte binary string (e.g., stored in a database).

```php
$id = ObjectId::fromBinary($binaryData);
```

#### From Timestamp
Generate an ID based on a specific time. useful for time-based sorting or filtering.

```php
// From a DateTime object
$id = ObjectId::fromTime(new DateTime('2025-01-01'));

// From a Unix timestamp
$id = ObjectId::fromTime(1735689600);
```

**Note:** By default, `fromTime` generates a unique ID (randomizing the remaining bytes). If you need a "zeroed" ID for range queries (e.g., "find all IDs created after X"), pass `unique: false`:

```php
$startId = ObjectId::fromTime($timestamp, unique: false);
// Last 8 bytes will be 0000000000000000
```

Invalid input to the `ObjectId::from...` series will throw `SlimeSystems\ObjectId\Exception\Invalid`.
They are safe for handling untrusted variables, assuming that this exception is the expected behavior.

### Conversions

```php
$id = new ObjectId;

// To Hex String (24 chars)
$hex = $id->toString(); 
// or simply: (string) $id

// To Binary (12 bytes)
$bin = $id->toBinary();

// To DateTime
$date = $id->toTime();
```

### Comparisons

#### Equality Check
Check if two ObjectIds represent the same value.

```php
if ($id1->equals($id2)) {
    // ...
}
```

#### Sorting
Compare two IDs lexicographically (useful for sorting).

```php
$result = $id1->compareTo($id2);
// -1 if $id1 < $id2
//  0 if $id1 == $id2
//  1 if $id1 > $id2
```

### Debugging
Get a readable inspection string.

```php
echo $id->inspect();
// SlimeSystems\ObjectId(507f1f77bcf86cd799439011)
```

## 🔌 Framework Integration

### Laravel / Eloquent
Using Laravel? Check out **[SlimeSystems\EloquentObjectId](https://github.com/slime-systems/eloquent-object-id)** for seamless integration with Eloquent models.

## 🧪 Testing

Run the test suite with:

```bash
composer run test
```

Or if you have containerd:

```bash
make test
```

## 📄 License

This project is licensed under the [BSD 2-Clause License](./LICENSE.md).
