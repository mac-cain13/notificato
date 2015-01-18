(function(root) {

    var bhIndex = null;
    var rootPath = '';
    var treeHtml = '    <ul>                <li data-name="namespace:" class="opened">                    <div style="padding-left:0px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href=".html">Wrep</a>                    </div>                    <div class="bd">                            <ul>                <li data-name="namespace:Wrep_Notificato" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Wrep/Notificato.html">Notificato</a>                    </div>                    <div class="bd">                            <ul>                <li data-name="namespace:Wrep_Notificato_Apns" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Wrep/Notificato/Apns.html">Apns</a>                    </div>                    <div class="bd">                            <ul>                <li data-name="namespace:Wrep_Notificato_Apns_Exception" >                    <div style="padding-left:54px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Wrep/Notificato/Apns/Exception.html">Exception</a>                    </div>                    <div class="bd">                            <ul>                <li data-name="class:Wrep_Notificato_Apns_Exception_InvalidCertificateException" >                    <div style="padding-left:80px" class="hd leaf">                        <a href="Wrep/Notificato/Apns/Exception/InvalidCertificateException.html">InvalidCertificateException</a>                    </div>                </li>                            <li data-name="class:Wrep_Notificato_Apns_Exception_ValidationException" >                    <div style="padding-left:80px" class="hd leaf">                        <a href="Wrep/Notificato/Apns/Exception/ValidationException.html">ValidationException</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:Wrep_Notificato_Apns_Feedback" >                    <div style="padding-left:54px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Wrep/Notificato/Apns/Feedback.html">Feedback</a>                    </div>                    <div class="bd">                            <ul>                <li data-name="class:Wrep_Notificato_Apns_Feedback_Feedback" >                    <div style="padding-left:80px" class="hd leaf">                        <a href="Wrep/Notificato/Apns/Feedback/Feedback.html">Feedback</a>                    </div>                </li>                            <li data-name="class:Wrep_Notificato_Apns_Feedback_FeedbackFactory" >                    <div style="padding-left:80px" class="hd leaf">                        <a href="Wrep/Notificato/Apns/Feedback/FeedbackFactory.html">FeedbackFactory</a>                    </div>                </li>                            <li data-name="class:Wrep_Notificato_Apns_Feedback_Tuple" >                    <div style="padding-left:80px" class="hd leaf">                        <a href="Wrep/Notificato/Apns/Feedback/Tuple.html">Tuple</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="class:Wrep_Notificato_Apns_Certificate" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Wrep/Notificato/Apns/Certificate.html">Certificate</a>                    </div>                </li>                            <li data-name="class:Wrep_Notificato_Apns_CertificateFactory" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Wrep/Notificato/Apns/CertificateFactory.html">CertificateFactory</a>                    </div>                </li>                            <li data-name="class:Wrep_Notificato_Apns_Gateway" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Wrep/Notificato/Apns/Gateway.html">Gateway</a>                    </div>                </li>                            <li data-name="class:Wrep_Notificato_Apns_GatewayFactory" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Wrep/Notificato/Apns/GatewayFactory.html">GatewayFactory</a>                    </div>                </li>                            <li data-name="class:Wrep_Notificato_Apns_Message" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Wrep/Notificato/Apns/Message.html">Message</a>                    </div>                </li>                            <li data-name="class:Wrep_Notificato_Apns_MessageBuilder" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Wrep/Notificato/Apns/MessageBuilder.html">MessageBuilder</a>                    </div>                </li>                            <li data-name="class:Wrep_Notificato_Apns_MessageEnvelope" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Wrep/Notificato/Apns/MessageEnvelope.html">MessageEnvelope</a>                    </div>                </li>                            <li data-name="class:Wrep_Notificato_Apns_Sender" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Wrep/Notificato/Apns/Sender.html">Sender</a>                    </div>                </li>                            <li data-name="class:Wrep_Notificato_Apns_SslSocket" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Wrep/Notificato/Apns/SslSocket.html">SslSocket</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="class:Wrep_Notificato_Notificato" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Wrep/Notificato/Notificato.html">Notificato</a>                    </div>                </li>                </ul></div>                </li>                </ul></div>                </li>                </ul>';

    var searchTypeClasses = {
        'Namespace': 'label-default',
        'Class': 'label-info',
        'Interface': 'label-primary',
        'Trait': 'label-success',
        'Method': 'label-danger',
        '_': 'label-warning'
    };

    var searchIndex = [
                    {"type": "Namespace", "link": "Wrep.html", "name": "Wrep", "doc": "Namespace Wrep"},{"type": "Namespace", "link": "Wrep/Notificato.html", "name": "Wrep\\Notificato", "doc": "Namespace Wrep\\Notificato"},{"type": "Namespace", "link": "Wrep/Notificato/Apns.html", "name": "Wrep\\Notificato\\Apns", "doc": "Namespace Wrep\\Notificato\\Apns"},{"type": "Namespace", "link": "Wrep/Notificato/Apns/Feedback.html", "name": "Wrep\\Notificato\\Apns\\Feedback", "doc": "Namespace Wrep\\Notificato\\Apns\\Feedback"},
            
            {"type": "Class", "fromName": "Wrep\\Notificato\\Apns", "fromLink": "Wrep/Notificato/Apns.html", "link": "Wrep/Notificato/Apns/Certificate.html", "name": "Wrep\\Notificato\\Apns\\Certificate", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Certificate", "fromLink": "Wrep/Notificato/Apns/Certificate.html", "link": "Wrep/Notificato/Apns/Certificate.html#method___construct", "name": "Wrep\\Notificato\\Apns\\Certificate::__construct", "doc": "&quot;APNS Certificate constructor&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Certificate", "fromLink": "Wrep/Notificato/Apns/Certificate.html", "link": "Wrep/Notificato/Apns/Certificate.html#method_getPemFile", "name": "Wrep\\Notificato\\Apns\\Certificate::getPemFile", "doc": "&quot;Get the path to the PEM file&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Certificate", "fromLink": "Wrep/Notificato/Apns/Certificate.html", "link": "Wrep/Notificato/Apns/Certificate.html#method_hasPassphrase", "name": "Wrep\\Notificato\\Apns\\Certificate::hasPassphrase", "doc": "&quot;Checks if there is a passphrase to use with the certificate&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Certificate", "fromLink": "Wrep/Notificato/Apns/Certificate.html", "link": "Wrep/Notificato/Apns/Certificate.html#method_getPassphrase", "name": "Wrep\\Notificato\\Apns\\Certificate::getPassphrase", "doc": "&quot;Passphrase to use with the PEM file&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Certificate", "fromLink": "Wrep/Notificato/Apns/Certificate.html", "link": "Wrep/Notificato/Apns/Certificate.html#method_getEnvironment", "name": "Wrep\\Notificato\\Apns\\Certificate::getEnvironment", "doc": "&quot;Get the APNS environment this certificate is associated with&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Certificate", "fromLink": "Wrep/Notificato/Apns/Certificate.html", "link": "Wrep/Notificato/Apns/Certificate.html#method_getDescription", "name": "Wrep\\Notificato\\Apns\\Certificate::getDescription", "doc": "&quot;An as humanreadable as possible description of the certificate to identify the certificate&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Certificate", "fromLink": "Wrep/Notificato/Apns/Certificate.html", "link": "Wrep/Notificato/Apns/Certificate.html#method_getValidFrom", "name": "Wrep\\Notificato\\Apns\\Certificate::getValidFrom", "doc": "&quot;Get moment this certificate will become valid\n Note: Will return null if certificate validation was disabled&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Certificate", "fromLink": "Wrep/Notificato/Apns/Certificate.html", "link": "Wrep/Notificato/Apns/Certificate.html#method_getValidTo", "name": "Wrep\\Notificato\\Apns\\Certificate::getValidTo", "doc": "&quot;Get moment this certificate will expire\n Note: Will return null if certificate validation was disabled&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Certificate", "fromLink": "Wrep/Notificato/Apns/Certificate.html", "link": "Wrep/Notificato/Apns/Certificate.html#method_getEndpoint", "name": "Wrep\\Notificato\\Apns\\Certificate::getEndpoint", "doc": "&quot;Get the endpoint this certificate is valid for&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Certificate", "fromLink": "Wrep/Notificato/Apns/Certificate.html", "link": "Wrep/Notificato/Apns/Certificate.html#method_getFingerprint", "name": "Wrep\\Notificato\\Apns\\Certificate::getFingerprint", "doc": "&quot;Get a unique hash of the certificate\n this can be used to check if two Apns\\Certificate objects are the same&quot;"},
            
            {"type": "Class", "fromName": "Wrep\\Notificato\\Apns", "fromLink": "Wrep/Notificato/Apns.html", "link": "Wrep/Notificato/Apns/CertificateFactory.html", "name": "Wrep\\Notificato\\Apns\\CertificateFactory", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\CertificateFactory", "fromLink": "Wrep/Notificato/Apns/CertificateFactory.html", "link": "Wrep/Notificato/Apns/CertificateFactory.html#method___construct", "name": "Wrep\\Notificato\\Apns\\CertificateFactory::__construct", "doc": "&quot;Create the CertificateFactory&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\CertificateFactory", "fromLink": "Wrep/Notificato/Apns/CertificateFactory.html", "link": "Wrep/Notificato/Apns/CertificateFactory.html#method_setDefaultCertificate", "name": "Wrep\\Notificato\\Apns\\CertificateFactory::setDefaultCertificate", "doc": "&quot;Set the default certificate&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\CertificateFactory", "fromLink": "Wrep/Notificato/Apns/CertificateFactory.html", "link": "Wrep/Notificato/Apns/CertificateFactory.html#method_getDefaultCertificate", "name": "Wrep\\Notificato\\Apns\\CertificateFactory::getDefaultCertificate", "doc": "&quot;Get the current default certificate&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\CertificateFactory", "fromLink": "Wrep/Notificato/Apns/CertificateFactory.html", "link": "Wrep/Notificato/Apns/CertificateFactory.html#method_createCertificate", "name": "Wrep\\Notificato\\Apns\\CertificateFactory::createCertificate", "doc": "&quot;Create a Certificate&quot;"},
            
            {"type": "Class", "fromName": "Wrep\\Notificato\\Apns\\Feedback", "fromLink": "Wrep/Notificato/Apns/Feedback.html", "link": "Wrep/Notificato/Apns/Feedback/Feedback.html", "name": "Wrep\\Notificato\\Apns\\Feedback\\Feedback", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Feedback\\Feedback", "fromLink": "Wrep/Notificato/Apns/Feedback/Feedback.html", "link": "Wrep/Notificato/Apns/Feedback/Feedback.html#method___construct", "name": "Wrep\\Notificato\\Apns\\Feedback\\Feedback::__construct", "doc": "&quot;Construct Connection&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Feedback\\Feedback", "fromLink": "Wrep/Notificato/Apns/Feedback/Feedback.html", "link": "Wrep/Notificato/Apns/Feedback/Feedback.html#method_receive", "name": "Wrep\\Notificato\\Apns\\Feedback\\Feedback::receive", "doc": "&quot;Receive the feedback tuples from APNS&quot;"},
            
            {"type": "Class", "fromName": "Wrep\\Notificato\\Apns\\Feedback", "fromLink": "Wrep/Notificato/Apns/Feedback.html", "link": "Wrep/Notificato/Apns/Feedback/FeedbackFactory.html", "name": "Wrep\\Notificato\\Apns\\Feedback\\FeedbackFactory", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Feedback\\FeedbackFactory", "fromLink": "Wrep/Notificato/Apns/Feedback/FeedbackFactory.html", "link": "Wrep/Notificato/Apns/Feedback/FeedbackFactory.html#method___construct", "name": "Wrep\\Notificato\\Apns\\Feedback\\FeedbackFactory::__construct", "doc": "&quot;Create the FeedbackFactory&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Feedback\\FeedbackFactory", "fromLink": "Wrep/Notificato/Apns/Feedback/FeedbackFactory.html", "link": "Wrep/Notificato/Apns/Feedback/FeedbackFactory.html#method_setCertificateFactory", "name": "Wrep\\Notificato\\Apns\\Feedback\\FeedbackFactory::setCertificateFactory", "doc": "&quot;Set a certificate factory to fetch the default certificate from&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Feedback\\FeedbackFactory", "fromLink": "Wrep/Notificato/Apns/Feedback/FeedbackFactory.html", "link": "Wrep/Notificato/Apns/Feedback/FeedbackFactory.html#method_getCertificateFactory", "name": "Wrep\\Notificato\\Apns\\Feedback\\FeedbackFactory::getCertificateFactory", "doc": "&quot;Get the current certificate factory&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Feedback\\FeedbackFactory", "fromLink": "Wrep/Notificato/Apns/Feedback/FeedbackFactory.html", "link": "Wrep/Notificato/Apns/Feedback/FeedbackFactory.html#method_createFeedback", "name": "Wrep\\Notificato\\Apns\\Feedback\\FeedbackFactory::createFeedback", "doc": "&quot;Create a Feedback object&quot;"},
            
            {"type": "Class", "fromName": "Wrep\\Notificato\\Apns\\Feedback", "fromLink": "Wrep/Notificato/Apns/Feedback.html", "link": "Wrep/Notificato/Apns/Feedback/Tuple.html", "name": "Wrep\\Notificato\\Apns\\Feedback\\Tuple", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Feedback\\Tuple", "fromLink": "Wrep/Notificato/Apns/Feedback/Tuple.html", "link": "Wrep/Notificato/Apns/Feedback/Tuple.html#method___construct", "name": "Wrep\\Notificato\\Apns\\Feedback\\Tuple::__construct", "doc": "&quot;Construct Tuple&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Feedback\\Tuple", "fromLink": "Wrep/Notificato/Apns/Feedback/Tuple.html", "link": "Wrep/Notificato/Apns/Feedback/Tuple.html#method_getInvalidatedAt", "name": "Wrep\\Notificato\\Apns\\Feedback\\Tuple::getInvalidatedAt", "doc": "&quot;Moment the device unregistered.&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Feedback\\Tuple", "fromLink": "Wrep/Notificato/Apns/Feedback/Tuple.html", "link": "Wrep/Notificato/Apns/Feedback/Tuple.html#method_getDeviceToken", "name": "Wrep\\Notificato\\Apns\\Feedback\\Tuple::getDeviceToken", "doc": "&quot;Get the device token of the device that unregistered&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Feedback\\Tuple", "fromLink": "Wrep/Notificato/Apns/Feedback/Tuple.html", "link": "Wrep/Notificato/Apns/Feedback/Tuple.html#method_getCertificate", "name": "Wrep\\Notificato\\Apns\\Feedback\\Tuple::getCertificate", "doc": "&quot;Get the certificate used while receiving this tuple&quot;"},
            
            {"type": "Class", "fromName": "Wrep\\Notificato\\Apns", "fromLink": "Wrep/Notificato/Apns.html", "link": "Wrep/Notificato/Apns/Gateway.html", "name": "Wrep\\Notificato\\Apns\\Gateway", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Gateway", "fromLink": "Wrep/Notificato/Apns/Gateway.html", "link": "Wrep/Notificato/Apns/Gateway.html#method___construct", "name": "Wrep\\Notificato\\Apns\\Gateway::__construct", "doc": "&quot;Construct Gateway&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Gateway", "fromLink": "Wrep/Notificato/Apns/Gateway.html", "link": "Wrep/Notificato/Apns/Gateway.html#method_queue", "name": "Wrep\\Notificato\\Apns\\Gateway::queue", "doc": "&quot;Queue a message for sending&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Gateway", "fromLink": "Wrep/Notificato/Apns/Gateway.html", "link": "Wrep/Notificato/Apns/Gateway.html#method_getQueueLength", "name": "Wrep\\Notificato\\Apns\\Gateway::getQueueLength", "doc": "&quot;Count of all queued messages&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Gateway", "fromLink": "Wrep/Notificato/Apns/Gateway.html", "link": "Wrep/Notificato/Apns/Gateway.html#method_flush", "name": "Wrep\\Notificato\\Apns\\Gateway::flush", "doc": "&quot;Send all queued messages&quot;"},
            
            {"type": "Class", "fromName": "Wrep\\Notificato\\Apns", "fromLink": "Wrep/Notificato/Apns.html", "link": "Wrep/Notificato/Apns/GatewayFactory.html", "name": "Wrep\\Notificato\\Apns\\GatewayFactory", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\GatewayFactory", "fromLink": "Wrep/Notificato/Apns/GatewayFactory.html", "link": "Wrep/Notificato/Apns/GatewayFactory.html#method_createGateway", "name": "Wrep\\Notificato\\Apns\\GatewayFactory::createGateway", "doc": "&quot;Create a Gateway object&quot;"},
            
            {"type": "Class", "fromName": "Wrep\\Notificato\\Apns", "fromLink": "Wrep/Notificato/Apns.html", "link": "Wrep/Notificato/Apns/Message.html", "name": "Wrep\\Notificato\\Apns\\Message", "doc": "&quot;An APNS Message representation.&quot;"},
                                                        {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Message", "fromLink": "Wrep/Notificato/Apns/Message.html", "link": "Wrep/Notificato/Apns/Message.html#method___construct", "name": "Wrep\\Notificato\\Apns\\Message::__construct", "doc": "&quot;Construct Message&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Message", "fromLink": "Wrep/Notificato/Apns/Message.html", "link": "Wrep/Notificato/Apns/Message.html#method_getDeviceToken", "name": "Wrep\\Notificato\\Apns\\Message::getDeviceToken", "doc": "&quot;Get the device token of the receiving device&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Message", "fromLink": "Wrep/Notificato/Apns/Message.html", "link": "Wrep/Notificato/Apns/Message.html#method_getCertificate", "name": "Wrep\\Notificato\\Apns\\Message::getCertificate", "doc": "&quot;Get the certificate that should be used for this message&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Message", "fromLink": "Wrep/Notificato/Apns/Message.html", "link": "Wrep/Notificato/Apns/Message.html#method_getExpiresAt", "name": "Wrep\\Notificato\\Apns\\Message::getExpiresAt", "doc": "&quot;Get the moment this message expires&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Message", "fromLink": "Wrep/Notificato/Apns/Message.html", "link": "Wrep/Notificato/Apns/Message.html#method_setExpiresAt", "name": "Wrep\\Notificato\\Apns\\Message::setExpiresAt", "doc": "&quot;Set the moment this message should expire or null if APNS should not store the message at all.&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Message", "fromLink": "Wrep/Notificato/Apns/Message.html", "link": "Wrep/Notificato/Apns/Message.html#method_setAlert", "name": "Wrep\\Notificato\\Apns\\Message::setAlert", "doc": "&quot;Set the alert to display.&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Message", "fromLink": "Wrep/Notificato/Apns/Message.html", "link": "Wrep/Notificato/Apns/Message.html#method_setAlertLocalized", "name": "Wrep\\Notificato\\Apns\\Message::setAlertLocalized", "doc": "&quot;Set the localized alert to display.&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Message", "fromLink": "Wrep/Notificato/Apns/Message.html", "link": "Wrep/Notificato/Apns/Message.html#method_getAlert", "name": "Wrep\\Notificato\\Apns\\Message::getAlert", "doc": "&quot;Get the current alert&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Message", "fromLink": "Wrep/Notificato/Apns/Message.html", "link": "Wrep/Notificato/Apns/Message.html#method_setBadge", "name": "Wrep\\Notificato\\Apns\\Message::setBadge", "doc": "&quot;Set the badge to display on the App icon&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Message", "fromLink": "Wrep/Notificato/Apns/Message.html", "link": "Wrep/Notificato/Apns/Message.html#method_clearBadge", "name": "Wrep\\Notificato\\Apns\\Message::clearBadge", "doc": "&quot;Clear the badge from the App icon&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Message", "fromLink": "Wrep/Notificato/Apns/Message.html", "link": "Wrep/Notificato/Apns/Message.html#method_getBadge", "name": "Wrep\\Notificato\\Apns\\Message::getBadge", "doc": "&quot;Get the value of the badge as set in this message&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Message", "fromLink": "Wrep/Notificato/Apns/Message.html", "link": "Wrep/Notificato/Apns/Message.html#method_setSound", "name": "Wrep\\Notificato\\Apns\\Message::setSound", "doc": "&quot;Set the sound that will be played when this message is received&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Message", "fromLink": "Wrep/Notificato/Apns/Message.html", "link": "Wrep/Notificato/Apns/Message.html#method_getSound", "name": "Wrep\\Notificato\\Apns\\Message::getSound", "doc": "&quot;Get the sound that will be played when this message is received&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Message", "fromLink": "Wrep/Notificato/Apns/Message.html", "link": "Wrep/Notificato/Apns/Message.html#method_setContentAvailable", "name": "Wrep\\Notificato\\Apns\\Message::setContentAvailable", "doc": "&quot;Set newsstand content availability flag that will trigger the newsstand item to download new content&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Message", "fromLink": "Wrep/Notificato/Apns/Message.html", "link": "Wrep/Notificato/Apns/Message.html#method_getContentAvailable", "name": "Wrep\\Notificato\\Apns\\Message::getContentAvailable", "doc": "&quot;Get newsstand content availability flag that will trigger the newsstand item to download new content&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Message", "fromLink": "Wrep/Notificato/Apns/Message.html", "link": "Wrep/Notificato/Apns/Message.html#method_setPayload", "name": "Wrep\\Notificato\\Apns\\Message::setPayload", "doc": "&quot;Set custom payload to go with the message&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Message", "fromLink": "Wrep/Notificato/Apns/Message.html", "link": "Wrep/Notificato/Apns/Message.html#method_getPayload", "name": "Wrep\\Notificato\\Apns\\Message::getPayload", "doc": "&quot;Get the current payload&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Message", "fromLink": "Wrep/Notificato/Apns/Message.html", "link": "Wrep/Notificato/Apns/Message.html#method_validateLength", "name": "Wrep\\Notificato\\Apns\\Message::validateLength", "doc": "&quot;Checks if the length of the message is acceptable for the APNS&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Message", "fromLink": "Wrep/Notificato/Apns/Message.html", "link": "Wrep/Notificato/Apns/Message.html#method_getJson", "name": "Wrep\\Notificato\\Apns\\Message::getJson", "doc": "&quot;Get the JSON payload that should be send to the APNS&quot;"},
            
            {"type": "Class", "fromName": "Wrep\\Notificato\\Apns", "fromLink": "Wrep/Notificato/Apns.html", "link": "Wrep/Notificato/Apns/MessageEnvelope.html", "name": "Wrep\\Notificato\\Apns\\MessageEnvelope", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\MessageEnvelope", "fromLink": "Wrep/Notificato/Apns/MessageEnvelope.html", "link": "Wrep/Notificato/Apns/MessageEnvelope.html#method___construct", "name": "Wrep\\Notificato\\Apns\\MessageEnvelope::__construct", "doc": "&quot;Construct MessageEnvelope&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\MessageEnvelope", "fromLink": "Wrep/Notificato/Apns/MessageEnvelope.html", "link": "Wrep/Notificato/Apns/MessageEnvelope.html#method_getIdentifier", "name": "Wrep\\Notificato\\Apns\\MessageEnvelope::getIdentifier", "doc": "&quot;Unique number to the relevant APNS connection to identify this message&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\MessageEnvelope", "fromLink": "Wrep/Notificato/Apns/MessageEnvelope.html", "link": "Wrep/Notificato/Apns/MessageEnvelope.html#method_getMessage", "name": "Wrep\\Notificato\\Apns\\MessageEnvelope::getMessage", "doc": "&quot;The message that&#039;s is contained by this envelope&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\MessageEnvelope", "fromLink": "Wrep/Notificato/Apns/MessageEnvelope.html", "link": "Wrep/Notificato/Apns/MessageEnvelope.html#method_getRetryLimit", "name": "Wrep\\Notificato\\Apns\\MessageEnvelope::getRetryLimit", "doc": "&quot;The number of times sending should be retried if it fails&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\MessageEnvelope", "fromLink": "Wrep/Notificato/Apns/MessageEnvelope.html", "link": "Wrep/Notificato/Apns/MessageEnvelope.html#method_getRetryEnvelope", "name": "Wrep\\Notificato\\Apns\\MessageEnvelope::getRetryEnvelope", "doc": "&quot;Get the envelope used for the retry&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\MessageEnvelope", "fromLink": "Wrep/Notificato/Apns/MessageEnvelope.html", "link": "Wrep/Notificato/Apns/MessageEnvelope.html#method_setStatus", "name": "Wrep\\Notificato\\Apns\\MessageEnvelope::setStatus", "doc": "&quot;Set the status of this message envelope\n only possible if there is no final state set yet.&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\MessageEnvelope", "fromLink": "Wrep/Notificato/Apns/MessageEnvelope.html", "link": "Wrep/Notificato/Apns/MessageEnvelope.html#method_getStatus", "name": "Wrep\\Notificato\\Apns\\MessageEnvelope::getStatus", "doc": "&quot;Get the current status of this message envelope&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\MessageEnvelope", "fromLink": "Wrep/Notificato/Apns/MessageEnvelope.html", "link": "Wrep/Notificato/Apns/MessageEnvelope.html#method_getStatusDescription", "name": "Wrep\\Notificato\\Apns\\MessageEnvelope::getStatusDescription", "doc": "&quot;Get a description of the current status of this message envelope&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\MessageEnvelope", "fromLink": "Wrep/Notificato/Apns/MessageEnvelope.html", "link": "Wrep/Notificato/Apns/MessageEnvelope.html#method_getFinalStatus", "name": "Wrep\\Notificato\\Apns\\MessageEnvelope::getFinalStatus", "doc": "&quot;Get the final status after all retries.&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\MessageEnvelope", "fromLink": "Wrep/Notificato/Apns/MessageEnvelope.html", "link": "Wrep/Notificato/Apns/MessageEnvelope.html#method_getFinalStatusDescription", "name": "Wrep\\Notificato\\Apns\\MessageEnvelope::getFinalStatusDescription", "doc": "&quot;Get a description of the final status after all retries.&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\MessageEnvelope", "fromLink": "Wrep/Notificato/Apns/MessageEnvelope.html", "link": "Wrep/Notificato/Apns/MessageEnvelope.html#method_getBinaryMessage", "name": "Wrep\\Notificato\\Apns\\MessageEnvelope::getBinaryMessage", "doc": "&quot;Get the message that this envelope contains in binary APNS compatible format&quot;"},
            
            {"type": "Class", "fromName": "Wrep\\Notificato\\Apns", "fromLink": "Wrep/Notificato/Apns.html", "link": "Wrep/Notificato/Apns/MessageFactory.html", "name": "Wrep\\Notificato\\Apns\\MessageFactory", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\MessageFactory", "fromLink": "Wrep/Notificato/Apns/MessageFactory.html", "link": "Wrep/Notificato/Apns/MessageFactory.html#method___construct", "name": "Wrep\\Notificato\\Apns\\MessageFactory::__construct", "doc": "&quot;Create the MessageFactory&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\MessageFactory", "fromLink": "Wrep/Notificato/Apns/MessageFactory.html", "link": "Wrep/Notificato/Apns/MessageFactory.html#method_setCertificateFactory", "name": "Wrep\\Notificato\\Apns\\MessageFactory::setCertificateFactory", "doc": "&quot;Set a certificate factory to fetch the default certificate from&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\MessageFactory", "fromLink": "Wrep/Notificato/Apns/MessageFactory.html", "link": "Wrep/Notificato/Apns/MessageFactory.html#method_getCertificateFactory", "name": "Wrep\\Notificato\\Apns\\MessageFactory::getCertificateFactory", "doc": "&quot;Get the current certificate factory&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\MessageFactory", "fromLink": "Wrep/Notificato/Apns/MessageFactory.html", "link": "Wrep/Notificato/Apns/MessageFactory.html#method_createMessage", "name": "Wrep\\Notificato\\Apns\\MessageFactory::createMessage", "doc": "&quot;Create a Message&quot;"},
            
            {"type": "Class", "fromName": "Wrep\\Notificato\\Apns", "fromLink": "Wrep/Notificato/Apns.html", "link": "Wrep/Notificato/Apns/Sender.html", "name": "Wrep\\Notificato\\Apns\\Sender", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Sender", "fromLink": "Wrep/Notificato/Apns/Sender.html", "link": "Wrep/Notificato/Apns/Sender.html#method___construct", "name": "Wrep\\Notificato\\Apns\\Sender::__construct", "doc": "&quot;Construct Sender&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Sender", "fromLink": "Wrep/Notificato/Apns/Sender.html", "link": "Wrep/Notificato/Apns/Sender.html#method_setGatewayFactory", "name": "Wrep\\Notificato\\Apns\\Sender::setGatewayFactory", "doc": "&quot;Set the gateway factory to use for creating connections to the APNS gateway&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Sender", "fromLink": "Wrep/Notificato/Apns/Sender.html", "link": "Wrep/Notificato/Apns/Sender.html#method_getGatewayFactory", "name": "Wrep\\Notificato\\Apns\\Sender::getGatewayFactory", "doc": "&quot;Get the current gateway factory&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Sender", "fromLink": "Wrep/Notificato/Apns/Sender.html", "link": "Wrep/Notificato/Apns/Sender.html#method_setLogger", "name": "Wrep\\Notificato\\Apns\\Sender::setLogger", "doc": "&quot;Sets a logger instance on the object&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Sender", "fromLink": "Wrep/Notificato/Apns/Sender.html", "link": "Wrep/Notificato/Apns/Sender.html#method_send", "name": "Wrep\\Notificato\\Apns\\Sender::send", "doc": "&quot;Queues a message and flushes the gateway connection it must be send over immediately\n Note: If you send multiple messages, queue as many as possible and flush them at once for maximum performance&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Sender", "fromLink": "Wrep/Notificato/Apns/Sender.html", "link": "Wrep/Notificato/Apns/Sender.html#method_queue", "name": "Wrep\\Notificato\\Apns\\Sender::queue", "doc": "&quot;Queue a message on the correct APNS gateway connection&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Sender", "fromLink": "Wrep/Notificato/Apns/Sender.html", "link": "Wrep/Notificato/Apns/Sender.html#method_getQueueLength", "name": "Wrep\\Notificato\\Apns\\Sender::getQueueLength", "doc": "&quot;Count of all queued messages&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\Sender", "fromLink": "Wrep/Notificato/Apns/Sender.html", "link": "Wrep/Notificato/Apns/Sender.html#method_flush", "name": "Wrep\\Notificato\\Apns\\Sender::flush", "doc": "&quot;Send all queued messages&quot;"},
            
            {"type": "Class", "fromName": "Wrep\\Notificato\\Apns", "fromLink": "Wrep/Notificato/Apns.html", "link": "Wrep/Notificato/Apns/SslSocket.html", "name": "Wrep\\Notificato\\Apns\\SslSocket", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\SslSocket", "fromLink": "Wrep/Notificato/Apns/SslSocket.html", "link": "Wrep/Notificato/Apns/SslSocket.html#method___construct", "name": "Wrep\\Notificato\\Apns\\SslSocket::__construct", "doc": "&quot;Construct Connection&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\SslSocket", "fromLink": "Wrep/Notificato/Apns/SslSocket.html", "link": "Wrep/Notificato/Apns/SslSocket.html#method_setLogger", "name": "Wrep\\Notificato\\Apns\\SslSocket::setLogger", "doc": "&quot;Sets a logger instance on the object&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Apns\\SslSocket", "fromLink": "Wrep/Notificato/Apns/SslSocket.html", "link": "Wrep/Notificato/Apns/SslSocket.html#method_getCertificate", "name": "Wrep\\Notificato\\Apns\\SslSocket::getCertificate", "doc": "&quot;Get the certificate used with this connection&quot;"},
            
            {"type": "Class", "fromName": "Wrep\\Notificato", "fromLink": "Wrep/Notificato.html", "link": "Wrep/Notificato/Notificato.html", "name": "Wrep\\Notificato\\Notificato", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Wrep\\Notificato\\Notificato", "fromLink": "Wrep/Notificato/Notificato.html", "link": "Wrep/Notificato/Notificato.html#method___construct", "name": "Wrep\\Notificato\\Notificato::__construct", "doc": "&quot;Notificato constructor&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Notificato", "fromLink": "Wrep/Notificato/Notificato.html", "link": "Wrep/Notificato/Notificato.html#method_createCertificate", "name": "Wrep\\Notificato\\Notificato::createCertificate", "doc": "&quot;Create an APNS Certificate&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Notificato", "fromLink": "Wrep/Notificato/Notificato.html", "link": "Wrep/Notificato/Notificato.html#method_createMessage", "name": "Wrep\\Notificato\\Notificato::createMessage", "doc": "&quot;Create a Message&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Notificato", "fromLink": "Wrep/Notificato/Notificato.html", "link": "Wrep/Notificato/Notificato.html#method_queue", "name": "Wrep\\Notificato\\Notificato::queue", "doc": "&quot;Queue a message on the correct APNS gateway connection&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Notificato", "fromLink": "Wrep/Notificato/Notificato.html", "link": "Wrep/Notificato/Notificato.html#method_flush", "name": "Wrep\\Notificato\\Notificato::flush", "doc": "&quot;Send all queued messages&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Notificato", "fromLink": "Wrep/Notificato/Notificato.html", "link": "Wrep/Notificato/Notificato.html#method_send", "name": "Wrep\\Notificato\\Notificato::send", "doc": "&quot;Queues a message and flushes the gateway connection it must be send over immediately\n Note: If you send multiple messages, queue as many as possible and flush them at once for maximum performance&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Notificato", "fromLink": "Wrep/Notificato/Notificato.html", "link": "Wrep/Notificato/Notificato.html#method_receiveFeedback", "name": "Wrep\\Notificato\\Notificato::receiveFeedback", "doc": "&quot;Receive the feedback tuples from APNS&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Notificato", "fromLink": "Wrep/Notificato/Notificato.html", "link": "Wrep/Notificato/Notificato.html#method_setSender", "name": "Wrep\\Notificato\\Notificato::setSender", "doc": "&quot;Sets the sender to use.&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Notificato", "fromLink": "Wrep/Notificato/Notificato.html", "link": "Wrep/Notificato/Notificato.html#method_setLogger", "name": "Wrep\\Notificato\\Notificato::setLogger", "doc": "&quot;Sets a logger instance on the object.&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Notificato", "fromLink": "Wrep/Notificato/Notificato.html", "link": "Wrep/Notificato/Notificato.html#method_setCertificateFactory", "name": "Wrep\\Notificato\\Notificato::setCertificateFactory", "doc": "&quot;Sets the certificate factory to use.&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Notificato", "fromLink": "Wrep/Notificato/Notificato.html", "link": "Wrep/Notificato/Notificato.html#method_setFeedbackFactory", "name": "Wrep\\Notificato\\Notificato::setFeedbackFactory", "doc": "&quot;Sets the feedback factory to use.&quot;"},
                    {"type": "Method", "fromName": "Wrep\\Notificato\\Notificato", "fromLink": "Wrep/Notificato/Notificato.html", "link": "Wrep/Notificato/Notificato.html#method_setMessageFactory", "name": "Wrep\\Notificato\\Notificato::setMessageFactory", "doc": "&quot;Sets the message factory to use.&quot;"},
            
            
                                        // Fix trailing commas in the index
        {}
    ];

    /** Tokenizes strings by namespaces and functions */
    function tokenizer(term) {
        if (!term) {
            return [];
        }

        var tokens = [term];
        var meth = term.indexOf('::');

        // Split tokens into methods if "::" is found.
        if (meth > -1) {
            tokens.push(term.substr(meth + 2));
            term = term.substr(0, meth - 2);
        }

        // Split by namespace or fake namespace.
        if (term.indexOf('\\') > -1) {
            tokens = tokens.concat(term.split('\\'));
        } else if (term.indexOf('_') > 0) {
            tokens = tokens.concat(term.split('_'));
        }

        // Merge in splitting the string by case and return
        tokens = tokens.concat(term.match(/(([A-Z]?[^A-Z]*)|([a-z]?[^a-z]*))/g).slice(0,-1));

        return tokens;
    };

    root.Sami = {
        /**
         * Cleans the provided term. If no term is provided, then one is
         * grabbed from the query string "search" parameter.
         */
        cleanSearchTerm: function(term) {
            // Grab from the query string
            if (typeof term === 'undefined') {
                var name = 'search';
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
                var results = regex.exec(location.search);
                if (results === null) {
                    return null;
                }
                term = decodeURIComponent(results[1].replace(/\+/g, " "));
            }

            return term.replace(/<(?:.|\n)*?>/gm, '');
        },

        /** Searches through the index for a given term */
        search: function(term) {
            // Create a new search index if needed
            if (!bhIndex) {
                bhIndex = new Bloodhound({
                    limit: 500,
                    local: searchIndex,
                    datumTokenizer: function (d) {
                        return tokenizer(d.name);
                    },
                    queryTokenizer: Bloodhound.tokenizers.whitespace
                });
                bhIndex.initialize();
            }

            results = [];
            bhIndex.get(term, function(matches) {
                results = matches;
            });

            if (!rootPath) {
                return results;
            }

            // Fix the element links based on the current page depth.
            return $.map(results, function(ele) {
                if (ele.link.indexOf('..') > -1) {
                    return ele;
                }
                ele.link = rootPath + ele.link;
                if (ele.fromLink) {
                    ele.fromLink = rootPath + ele.fromLink;
                }
                return ele;
            });
        },

        /** Get a search class for a specific type */
        getSearchClass: function(type) {
            return searchTypeClasses[type] || searchTypeClasses['_'];
        },

        /** Add the left-nav tree to the site */
        injectApiTree: function(ele) {
            ele.html(treeHtml);
        }
    };

    $(function() {
        // Modify the HTML to work correctly based on the current depth
        rootPath = $('body').attr('data-root-path');
        treeHtml = treeHtml.replace(/href="/g, 'href="' + rootPath);
        Sami.injectApiTree($('#api-tree'));
    });

    return root.Sami;
})(window);

$(function() {

    // Enable the version switcher
    $('#version-switcher').change(function() {
        window.location = $(this).val()
    });

    
        // Toggle left-nav divs on click
        $('#api-tree .hd span').click(function() {
            $(this).parent().parent().toggleClass('opened');
        });

        // Expand the parent namespaces of the current page.
        var expected = $('body').attr('data-name');

        if (expected) {
            // Open the currently selected node and its parents.
            var container = $('#api-tree');
            var node = $('#api-tree li[data-name="' + expected + '"]');
            // Node might not be found when simulating namespaces
            if (node.length > 0) {
                node.addClass('active').addClass('opened');
                node.parents('li').addClass('opened');
                var scrollPos = node.offset().top - container.offset().top + container.scrollTop();
                // Position the item nearer to the top of the screen.
                scrollPos -= 200;
                container.scrollTop(scrollPos);
            }
        }

    
    
        var form = $('#search-form .typeahead');
        form.typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            name: 'search',
            displayKey: 'name',
            source: function (q, cb) {
                cb(Sami.search(q));
            }
        });

        // The selection is direct-linked when the user selects a suggestion.
        form.on('typeahead:selected', function(e, suggestion) {
            window.location = suggestion.link;
        });

        // The form is submitted when the user hits enter.
        form.keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').submit();
                return true;
            }
        });

    
});


