<?php

namespace ApacheSolrForTypo3\Solr\System\Logging;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2016 Timo Hund <timo.hund@dkd.de
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use ApacheSolrForTypo3\Solr\Util;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * The DevLogDebugWriter is used to write the devLog messages to the output of the page, or to the TYPO3 console in the
 * backend to provide a simple and lightweigt debugging possibility.
 *
 * @author Timo Hund <timo.hund@dkd.de>
 */
class DevLogDebugWriter
{

    /**
     * When the feature is enabled with: plugin.tx_solr.logging.debugDevlogOutput the log writer uses the extbase
     * debug functionality in the frontend, or the console in the backend to display the devlog messages.
     *
     * @param array $parameters
     */
    public function log($parameters)
    {
        $extensionKey = isset($parameters['extKey']) ? $parameters['extKey'] : '';

        $isLogMessageFromSolr = $extensionKey === 'solr';
        if (!$isLogMessageFromSolr) {
            return;
        }

        $debugAllowedForIp = $this->getIsAllowedByDevIPMask();
        if (!$debugAllowedForIp) {
            return;
        }

        $isDebugOutputEnabled = $this->getIsDevLogDebugOutputEnabled();
        if (!$isDebugOutputEnabled) {
            return;
        }

        $this->writeDebugMessage($parameters);
    }

    /**
     * @return bool
     */
    protected function getIsAllowedByDevIPMask()
    {
        return GeneralUtility::cmpIP(GeneralUtility::getIndpEnv('REMOTE_ADDR'), $GLOBALS['TYPO3_CONF_VARS']['SYS']['devIPmask']);
    }

    /**
     * @return bool
     */
    protected function getIsDevLogDebugOutputEnabled()
    {
        return Util::getSolrConfiguration()->getLoggingDebugOutputDevlog();
    }

    /**
     * @param $parameters
     */
    protected function writeDebugMessage($parameters)
    {
        $message = isset($parameters['msg']) ? $parameters['msg'] : '';
        if (TYPO3_MODE == 'BE') {
            DebugUtility::debug($parameters, $parameters['extKey'], 'DevLog ext:solr: ' . $message);
        } else {
            echo $message . ':<br/>';
            DebuggerUtility::var_dump($parameters);
        }
    }
}
