<?php
/**
 * Get access to the Lizmap project metadata.
 *
 * @author    3liz
 * @copyright 2012-2021 3liz
 *
 * @see      http://3liz.com
 *
 * @license Mozilla Public License : http://www.mozilla.org/MPL/
 */

namespace Lizmap\Project;

use Lizmap\App;

class ProjectMainData
{
    /**
     * The project metadata.
     *
     * @var array
     */
    protected $data;

    /**
     * constructor.
     *
     * @param string                  $file       the QGIS project path
     * @param \lizmapServices         $services
     * @param App\AppContextInterface $appContext
     * @param mixed                   $data
     */
    public function __construct($repository, $id, $file, $requiredTargetLwcVersion, $appContext)
    {
        // Verifying if the files exist
        if (!file_exists($file)) {
            throw new UnknownLizmapProjectException('The QGIS project '.$file.' does not exist!');
        }
        if (!file_exists($file.'.cfg')) {
            throw new UnknownLizmapProjectException('The lizmap config '.$file.'.cfg does not exist!');
        }

        $qgsMtime = filemtime($file);
        $qgsCfgMtime = filemtime($file.'.cfg');
        $cacheHandler = new ProjectCache($file, $qgsMtime, $qgsCfgMtime, $appContext);
        $cachedData = $cacheHandler->retrieveProjectData();

        $data = array(
            'repository' => $repository,
            'id' => $id,
            'title' => ucfirst($id),
            'abstract' => '',
            'keywordList' => '',
        );

        if ($cachedData === false) {
            $this->data = array_merge(
                $data,
                $this->readXmlProject($file),
                $this->readCfgProject($file, $requiredTargetLwcVersion, $appContext),
            );
        } else {
            $this->data = array_merge(
                $data,
                $this->readCachedData($cachedData, $requiredTargetLwcVersion, $appContext),
            );
        }

        $this->data['wmsGetCapabilitiesUrl'] = $appContext->getFullUrl(
            'lizmap~service:index',
            array(
                'repository' => $repository,
                'project' => $id,
                'SERVICE' => 'WMS',
                'VERSION' => '1.3.0',
                'REQUEST' => 'GetCapabilities',
            )
        );
        $this->data['wmtsGetCapabilitiesUrl'] = $appContext->getFullUrl(
            'lizmap~service:index',
            array(
                'repository' => $repository,
                'project' => $id,
                'SERVICE' => 'WMTS',
                'VERSION' => '1.0.0',
                'REQUEST' => 'GetCapabilities',
            )
        );

        // Check right on repository
        if ($this->data['acl'] && !$appContext->aclCheck('lizmap.repositories.view', $repository)) {
            $this->data['acl'] = false;
        }
    }

    /**
     * Get the project id.
     *
     * @return string the project id
     */
    public function getId()
    {
        return $this->data['id'];
    }

    /**
     * Get the project repository code.
     *
     * @return string the project repository code
     */
    public function getRepository()
    {
        return $this->data['repository'];
    }

    /**
     * Get the project title.
     *
     * @return string the project title
     */
    public function getTitle()
    {
        return $this->data['title'];
    }

    /**
     * Get the project abstract.
     *
     * @return string the project abstract
     */
    public function getAbstract()
    {
        return $this->data['abstract'];
    }

    /**
     * List of keywords.
     *
     * @return array
     */
    public function getKeywordList()
    {
        return $this->data['keywordList'];
    }

    /**
     * FIXME what is the returned content ?
     *
     * @return mixed
     */
    public function getProj()
    {
        return $this->data['proj'];
    }

    /**
     * Get the bounding box.
     *
     * FIXME what is the returned content ?
     *
     * @return mixed
     */
    public function getBbox()
    {
        return $this->data['bbox'];
    }

    /**
     * Check if the project needs an update which is critical.
     *
     * @return bool true if the project needs an update
     */
    public function needsUpdateError()
    {
        return $this->data['needsUpdateError'];
    }

    /**
     * Get the project access rights for the authenticated or anonymous user.
     *
     * @return bool True if the user has the right to access the Lizmap project
     */
    public function getAcl()
    {
        return $this->data['acl'];
    }

    /**
     * Get the project hidden flag.
     *
     * @return bool True if the project must be hidden in the landing page
     */
    public function getHidden()
    {
        return $this->data['hidden'];
    }

    /**
     * The url of WMS GetCapabilities.
     *
     * @return string
     */
    public function getWMSGetCapabilitiesUrl()
    {
        return $this->data['wmsGetCapabilitiesUrl'];
    }

    /**
     * The url of WMTS GetCapabilities.
     *
     * @return string
     */
    public function getWMTSGetCapabilitiesUrl()
    {
        return $this->data['wmtsGetCapabilitiesUrl'];
    }

    public function getData()
    {
        return array_merge(array(), $this->data);
    }

    protected function readXmlProject($qgs_path)
    {
        $oXml = new \XMLReader();

        // Open file
        if (!$oXml->open($qgs_path)) {
            throw new UnknownLizmapProjectException('The file '.$qgs_path.' cannot be parsed.');
        }

        $data = array();
        // Read until we are at the root document element
        while ($oXml->read()) {
            if ($oXml->nodeType == \XMLReader::ELEMENT
                && $oXml->depth == 1
                && $oXml->localName == 'properties') {
                $data = array_merge($data, $this->readXmlProperties($oXml));
            }
        }
        $errorMsg = '';
        foreach (libxml_get_errors() as $error) {
            if ($errorMsg !== '') {
                $errorMsg .= '\n';
            }
            switch ($error->level) {
                case LIBXML_ERR_WARNING:
                    $errorMsg .= 'Warning '.$error->code.': ';
                    break;
                case LIBXML_ERR_ERROR:
                    $errorMsg .= 'Error '.$error->code.': ';
                    break;
                case LIBXML_ERR_FATAL:
                    $errorMsg .= 'Fatal Error '.$error->code.': ';
                    break;
            }
            $errorMsg .= 'Line: '.$error->line.' ';
            $errorMsg .= 'Column: '.$error->column.' ';
            $errorMsg .= trim($error->message);
        }
        // Clear libxml error buffer
        libxml_clear_errors();

        if ($errorMsg !== '') {
            throw new \Exception($errorMsg);
        }

        return $data;
    }

    protected function readXmlProperties($oXmlReader)
    {
        $depth = $oXmlReader->depth;
        $localName = $oXmlReader->localName;
        if ($oXmlReader->isEmptyElement) {
            return array();
        }

        $data = array();
        while ($oXmlReader->read()) {
            if ($oXmlReader->nodeType == \XMLReader::END_ELEMENT
                && $oXmlReader->localName == $localName
                && $oXmlReader->depth == $depth) {
                break;
            }

            if ($oXmlReader->nodeType != \XMLReader::ELEMENT) {
                continue;
            }

            if ($oXmlReader->depth != $depth + 1) {
                continue;
            }

            if ($oXmlReader->localName == 'WMSServiceTitle') {
                $data['title'] = $oXmlReader->readString();
            } else if ($oXmlReader->localName == 'WMSServiceAbstract') {
                $data['abstract'] = $oXmlReader->readString();
            } else if  ($oXmlReader->localName == 'WMSKeywordList') {
                $type = $oXmlReader->getAttribute('type');
                if ($type == 'QStringList') {
                    if (!$oXmlReader->isEmptyElement) {
                        $data['keywordList'] = implode(', ', $this->readValues($oXmlReader));
                    }
                }
                $data['keywordList'] = '';
            }
        }

        return $data;
    }

    protected function readValues($oXmlReader)
    {
        if ($oXmlReader->nodeType != \XMLReader::ELEMENT) {
            throw new \Exception('Provide an XMLReader::ELEMENT!');
        }

        $localName = $oXmlReader->localName;
        $depth = $oXmlReader->depth;
        $values = array();
        while ($oXmlReader->read()) {
            if ($oXmlReader->nodeType == \XMLReader::END_ELEMENT
                && $oXmlReader->localName == $localName
                && $oXmlReader->depth == $depth) {
                break;
            }

            if ($oXmlReader->nodeType != \XMLReader::ELEMENT) {
                continue;
            }

            if ($oXmlReader->depth != $depth + 1) {
                continue;
            }

            if ($oXmlReader->localName == 'value') {
                $values[] = $oXmlReader->readString();
            }
        }

        return $values;
    }

    protected function readCfgProject($qgs_path, $requiredTargetLwcVersion, $appContext)
    {
        $fileContent = file_get_contents($qgs_path.'.cfg');
        $cfgContent = json_decode($fileContent);
        if ($cfgContent === null) {
            throw new UnknownLizmapProjectException('The file '.$qgs_path.'.cfg cannot be decoded.');
        }

        return array(
            'proj' => $this->getProjOption($cfgContent),
            'bbox' => $this->getBboxOption($cfgContent),
            'needsUpdateError' => $this->getNeedsUpdateError($cfgContent, $requiredTargetLwcVersion),
            'acl' => $this->checkAcl($cfgContent, $appContext),
            'hidden' => $this->getHiddenOption($cfgContent),
        );
    }

    protected function getProjOption($cfgContent)
    {
        if (property_exists($cfgContent, 'options')
            && property_exists($cfgContent->options, 'projection')
            && property_exists($cfgContent->options->projection, 'ref')) {
            return $cfgContent->options->projection->ref;
        }
        return '';
    }

    protected function getBboxOption($cfgContent)
    {
        if (property_exists($cfgContent, 'options')
            && property_exists($cfgContent->options, 'bbox')) {
            return implode(', ', $cfgContent->options->bbox);
        }
        return '';
    }

    protected function getNeedsUpdateError($cfgContent, $requiredTargetLwcVersion) {
        $lizmapWebClientTargetVersion = 30200;
        if (property_exists($cfgContent, 'metadata')
            && property_exists($cfgContent->metadata, 'lizmap_web_client_target_version')) {
            $lizmapWebClientTargetVersion = $cfgContent->metadata->lizmap_web_client_target_version;
        }
        return $lizmapWebClientTargetVersion < $requiredTargetLwcVersion;
    }

    protected function checkAcl($cfgContent, $appContext)
    {
        // Check acl option is configured in project config
        $aclGroups = null;
        if (property_exists($cfgContent, 'options')
            && property_exists($cfgContent->options, 'acl')) {
            $aclGroups = $cfgContent->options->acl;
        }
        if ($aclGroups === null || !is_array($aclGroups) || empty($aclGroups)) {
            return true;
        }

        // Check user is authenticated
        if (!$appContext->userIsConnected()) {
            return false;
        }

        // Check if configured groups white list and authenticated user groups list intersects
        $userGroups = $appContext->aclUserGroupsId();
        if (array_intersect($aclGroups, $userGroups)) {
            return true;
        }

        return false;
    }

    protected function getHiddenOption($cfgContent)
    {
        $value = '';
        if (property_exists($cfgContent, 'options')
            && property_exists($cfgContent->options, 'hideProject')) {
            $value = $cfgContent->options->hideProject;
        }
        if (empty($value)) {
            return false;
        }
        if (is_bool($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return intval($value) > 0;
        }
        $strVal = strtolower($value);

        return in_array($strVal, array('true', 't', 'on', '1'));
    }

    protected function readCachedData($cachedData, $requiredTargetLwcVersion, $appContext)
    {
        $cfgContent = $cachedData['cfg'];

        $data = array(
            'proj' => $this->getProjOption($cfgContent),
            'bbox' => $this->getBboxOption($cfgContent),
            'needsUpdateError' => $this->getNeedsUpdateError($cfgContent, $requiredTargetLwcVersion),
            'acl' => $this->checkAcl($cfgContent, $appContext),
            'hidden' => $this->getHiddenOption($cfgContent),
        );

        $qgisCachedData = $cachedData['qgis']['data'];
        if (array_key_exists('title', $qgisCachedData)) {
            $data['title'] = $qgisCachedData['title'];
        }
        if (array_key_exists('abstract', $qgisCachedData)) {
            $data['abstract'] = $qgisCachedData['abstract'];
        }
        if (array_key_exists('keywordList', $qgisCachedData)) {
            $data['keywordList'] = $qgisCachedData['keywordList'];
        }
        return $data;
    }
}
