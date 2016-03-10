<?php
if (defined('TYPO3_mode')) {
    die();
}

if (!function_exists('sentry_register')) {
    /**
     * Registers exception handler to the Sentry.
     *
     * @return void
     */
    function sentry_register()
    {
        $extConf = @unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sentry']);
        if (is_array($extConf) && isset($extConf['sentryDSN'])) {
            // Register Raven autoloader
            $ravenPhpAutoloaderPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('sentry',
                'lib/raven-php/lib/Raven/Autoloader.php');
            /** @noinspection PhpIncludeInspection */
            require_once($ravenPhpAutoloaderPath);
            Raven_Autoloader::register();

            // Set error handler
            $options = array();
            $options['http_proxy'] = $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyIP'];
            if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyServer'] !== '') {
                $options['http_proxy'] = $GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyServer'];
            }

            $GLOBALS['SENTRY_CLIENT'] = new Raven_Client($extConf['sentryDSN'], $options);
            $ravenErrorHandler = new Raven_ErrorHandler($GLOBALS['SENTRY_CLIENT']);

            $errorMask = E_ALL & ~(E_DEPRECATED | E_NOTICE | E_STRICT);

            // Register handlers in case if we do not have to report to TYPO3. Otherwise we need to register those handlers first!
            if (!$extConf['passErrorsToTypo3']) {
                $ravenErrorHandler->registerErrorHandler(false, $errorMask);
                $ravenErrorHandler->registerExceptionHandler(false);
            }

            // Make sure that TYPO3 does not override our handler
            \MoveElevator\Sentry\ErrorHandlers\SentryErrorHandler::initialize($ravenErrorHandler, $errorMask);
            \MoveElevator\Sentry\ErrorHandlers\SentryExceptionHandler::initialize($ravenErrorHandler);

            if (version_compare(TYPO3_branch, '7.0', '>=')) {
                \MoveElevator\Sentry\ErrorHandlers\SentryExceptionHandlerFrontend::initialize($ravenErrorHandler);
            }

            // Register test plugin
            if (is_array($extConf) && isset($extConf['enableTestPlugin']) && $extConf['enableTestPlugin']) {
                \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin('MoveElevator.sentry', 'ErrorHandlerTest',
                    array('ErrorHandlerTest' => 'index,phpWarning,phpError,phpException'),
                    array('ErrorHandlerTest' => 'index,phpWarning,phpError,phpExceptionMoveElevator'));
            }
            unset($extConf);

            // Fix TYPO3 7.0 hard-coded FE exception handler
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Frontend\\ContentObject\\Exception\\ProductionExceptionHandler'] = array(
                'className' => 'MoveElevator\\Sentry\\ErrorHandlers\\SentryExceptionHandlerFrontend',
            );
        }
    }

    sentry_register();
}
