# Upgrade guide

## From 1.0 to 1.1

### Message creation

**Backwards incompatible changes:**

* The `Message`-object is now read-only, the setters are removed
* The `Message::validateLength` method is gone, as the constructor now validates the length on creation
* The `MessageBuilder` class is introduced to create `Message`-objects
* The `MessageFactory` class is removed in favour of the `MessageBuilder`-class

*New features:*

* `Message` is now serializeable for easy storage
* `Message::__toString` is implemented and dumps the contents of the message for debugging

### Certificates

*New features:*

* `Certificate::isValidated` is introduced and indicates if the certificate was validated on construction

### Sending and status

**Backwards incompatible changes:**

* `MessageEnvelope::STATUS_PAYLOADTOOLONG` status is removed as constructing messages immediatly throws an exception