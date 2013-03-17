# Generate APNS Certificate
To push to your iOS/Mac App or to Passbook you'll need to generate an APNS certificate in the [Apple developer portal](https://developer.apple.com/). This page will guide you through this process.

*Note that Apple also has some usefull instructions on [Provisioning and Development](http://developer.apple.com/library/ios/#documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/ProvisioningDevelopment/ProvisioningDevelopment.html#//apple_ref/doc/uid/TP40008194-CH104-SW1).*

## 1. Create a certificate signing request
You have two options to create your certificate signing request (CSR), with Keychain App on your Mac or using the commandline.

### a. Using Keychain on your Mac
1. Open the Keychain App
2. Choose from the menu: Keychain > Certificate assistent > Request certificate from certificate authority…
3. Fill out the e-mail and name you want in the certificate and choose "Save to disk"
4. Go ahead and save the file somewhere you can find it again

### b. Using the commandline
1. Open the terminal and go to a folder where you can put the certificate files
2. Generate a new private key and CSR: `openssl req -nodes -newkey rsa:2048 -keyout private.plain.key -out certrequest.csr -subj "/emailAddress=email/CN=Name/C=US"`
3. Secure the private key with an passphrase: `openssl rsa -in private.plain.key -des3 -out private.key`

## 2. Generate the certificate
Now that you have your CSR head over to the [iOS](https://developer.apple.com/ios) or [Mac](https://developer.apple.com/mac) developer portal, then go to the "Provisioning Portal".

### a. For your iOS App
1. Click "App IDs"
2. Look up/create the correct App ID and click "Configure"
3. Check the "Enable for Apple Push Notification service" box
4. Click "Configure" behind the Production or the Development Push SSL Certificate
5. Click "Continue", upload the CSR you just generated
6. Click "Generate", wait for the certificate to be generated and then download it!

*Note: You can't use push with App IDs that have wildcards in them!*

*Note: You must update all provisioning profiles with this App ID before push notifications will work! See also the ["Creating and Installing the Provisioning Profile"](http://developer.apple.com/library/ios/#documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/ProvisioningDevelopment/ProvisioningDevelopment.html%23//apple_ref/doc/uid/TP40008194-CH104-SW1) section.*

### b. For your Passbook Pass
1. Click "Pass Type IDs"
2. Look up/create the correct Pass Type ID and click "Configure"
4. Click "Configure" behind the Pass Certificate
5. Click "Continue", upload the CSR you just generated
6. Click "Generate", wait for the certificate to be generated and then download it!

*Note: The certificate to sign your pass with is exactly the same certificate used for push!*

## 3. Export certificate to PEM
You now have your certificate, time to convert it to a Notificare compatible format.

### a. If you used Keychain for CSR generation
1. Click the `.cer`-file so Keychain will import it
2. Open Keychain and lookup the certificate
3. Select **both** the certificate and the private key associated with it
4. Right click on the selection and choose "Export 2 items…"
5. Choose "Personal Information Exchange (.p12)" format and save it to disk as "keychainexport.p12"
6. Convert the `.p12`-file to `.pem` format by running: `openssl pkcs12 -in keychainexport.p12 -out certificate.pem`

*Note: This will first ask for the passphrase you encrypted the p12 with while exporting from Keychain, then it will ask for a new passphrase to encrypt the pem-file with.*

### b. If you used the commandline for CSR generation
1. Make sure the downloaded `.cer`-file is in the same folder as the other generated files
2. Open the terminal and go to the folder the certificate files are in
3. Convert Apples certificate to PEM format: `openssl x509 -inform der -in aps_development.cer -out aps_development.pem`
3. Then add the key and certificate together: `cat aps_development.pem private.key > certificate.pem`

Now `certificate.pem` is the certificate file you can use to push messages with, of course it will only work with choosen APNS environment and App/Passbook Pass you generated the certificate for. [Now go push something!](push.md)