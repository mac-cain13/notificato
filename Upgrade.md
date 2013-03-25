# Upgrade guide

## From 1.0 to 1.1

### Message creation

* The `Message`-object is now read-only, the setters are removed
* A `MessageBuilder`-class is introduced to create `Message`-objects
* The `MessageFactory`-class is removed in favour of the `MessageBuilder`-class
