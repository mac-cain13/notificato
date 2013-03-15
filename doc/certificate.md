# Generate APNS Certificate
To push to your iOS App or to Passbook you need to generate an APNS certificate in the [Apple iOS developer portal](https://developer.apple.com/ios). This page will guide you through this provess

## 1. Create a certificate signing request
You have two options to create your certificate signing request (CSR), with Keychain App on your Mac or using the commandline.

### a. Using keychain on your Mac
1. Open the Keychain App
2. Choose from the menu: Keychain > Certificate assistent > Request certificate from certificate authority…
3. Fill out the e-mail and name you want in the certificate and choose "Save to disk"
4. Go ahead and save the file somewhere you can find it again

### b. Using the commandline
1. Open the terminal and go to a folder where you can put the certificate files
2. Run `openssl req -nodes -newkey rsa:2048 -keyout apns-private.plain.key -out apns-request.csr -subj "/emailAddress=email@example.com/CN=Your Name/C=US"`
3. Secure the private key with an passphrase: `openssl rsa -in apns-private.plain.key -des3 -out apns-private.key`

## 2. Generate the certificate
Now that you have your CSR head over to the [iOS developer portal](https://developer.apple.com/ios), then go to the "iOS Provisioning Portal".

### a. For your App
1. Click "App IDs"
2. Look up/create the correct App ID and click "Configure"
3. Check the "Enable for Apple Push Notification service" box
4. Click "Configure" behind the Production or the Development Push SSL Certificate
5. Click "Continue", upload the CSR file you just generated
6. Click "Generate", wait for the certificate to be generated and then download it!

*Note: You can't use push with App IDs that have wildcards in them!*

### b. For your Passbook Pass
1. Click "Pass Type IDs"
2. Look up/create the correct Pass Type ID and click "Configure"
4. Click "Configure" behind the Pass Certificate
5. Click "Continue", upload the CSR file you just generated
6. Click "Generate", wait for the certificate to be generated and then download it!

*Note: The certificate to sign your pass with is exactly the same certificate used for push!*

## 3. Export certificate to PEM
You now have your certificate, time to convert it to a Notificare compatible format.

### a. If you used keychain for CSR generation
1. Click the `.cer`-file so keychain will import it
2. Open keychain and lookup the certificate
3. Select **both** the certificate and the private key associated with it
4. Right click and choose "Export 2 items…"
5. Choose "Personal Information Exchange (.p12)" format and save it to disk as "apns-keychainexport.p12"
6. Convert the `.p12`-file to `.pem` format by running: `openssl pkcs12 -in apns-keychainexport.p12 -out apns-certificate.pem`

**Note: This will first ask for the passphrase you encryptes the p12 with while exporting from keychain, then it will ask for a new passphrase to encrypt the pem-file with.**

### b. If you used the commandline for CSR generation
1. Make sure the downloaded `.cer`-file is in the same folder as the other generated files
2. Open the terminal and go to the folder the certificate files are in
3. Run `cat apns-development.cer apns-private.key > apns-certificate.pem`

Now you have your certificate to push to the choosen APNS environment for the App/Passbook Pass you generated this certificate for. Now go on and push something!