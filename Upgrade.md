# Upgrade guide
This document gives an overview of what's changed between versions. Backwards incompatible changes are described as wel as the most important new features.

## From 1.1 to 1.2

No breaking changes, just support for the new production certificates from Apple.

## From 1.0 to 1.1

### Message creation

**Backwards incompatible changes:**

* The `Message`-object is now read-only, all setters are removed
* The `MessageBuilder` class is introduced to create `Message`-objects
* The `MessageFactory` class is removed in favour of the `MessageBuilder`-class
* The `Message::validateLength` method is gone, as the constructor now validates the length on creation

*New features:*

* `Message` is now serializable for easy storage
* `Message::__toString` is implemented and dumps the contents of the message for debugging

### Certificates

**Backwards incompatible changes:**

* If the certificate is invalid the class now throws an `InvalidCertificateException` instead of `InvalidArgumentException`

*New features:*

* `Certificate::isValidated` is introduced and indicates if the certificate was validated on construction

### Sending and status

**Backwards incompatible changes:**

* `MessageEnvelope::STATUS_PAYLOADTOOLONG` status is removed as constructing messages immediatly throws an exception