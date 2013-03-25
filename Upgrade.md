# Upgrade guide

## From 1.0 to 1.1

### Message creation

* The `Message`-object is now read-only, the setters are removed
* The `Message::validateLength` method is gone, as the constructor now validates the length on creation
* A `MessageBuilder`-class is introduced to create `Message`-objects
* The `MessageFactory`-class is removed in favour of the `MessageBuilder`-class

### Sending and status

* `MessageEnvelope::STATUS_PAYLOADTOOLONG` status is removed as constructing messages that are too long is now impossible