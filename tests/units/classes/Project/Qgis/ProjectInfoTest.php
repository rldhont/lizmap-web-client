<?php

use PHPUnit\Framework\TestCase;
use Lizmap\Project\Qgis;
use Lizmap\App;

/**
 * @internal
 * @coversNothing
 */
class ProjectInfoTest extends TestCase
{
    /*public function testConstruct()
    {
        $data = array(
            'WMSServiceTitle' => 'Montpellier - Transports',
            'WMSServiceAbstract' => 'Demo project with bus and tramway lines in Montpellier, France.
Data is licensed under ODbl, OpenStreetMap contributors',
            'WMSKeywordList' => array(''),
            'WMSExtent' => array('417006.61373760335845873', '5394910.34090302512049675', '447158.04891100589884445', '5414844.99480544030666351'),
            'WMSOnlineResource' => 'http://www.3liz.com/lizmap.html',
            'WMSContactMail' => 'info@3liz.com',
            'WMSContactOrganization' => '3liz',
            'WMSContactPerson' => '3liz',
            'WMSContactPhone' => '+334 67 16 64 51',
            'WMSRestrictedComposers' => array('Composeur1'),
            'WMSRestrictedLayers' => array(),
            'WMSUseLayerIDs' => false,
        );

        $properties = new Qgis\ProjectProperties($data);
        foreach($data as $prop => $value) {
            $this->assertEquals($value, $properties->$prop);
        }
    }*/

    public function testFromXmlReader()
    {
        /*$xmlStr = '
        <properties>
          <WMSContactMail type="QString">info@3liz.com</WMSContactMail>
          <WMSContactOrganization type="QString">3liz</WMSContactOrganization>
          <WMSContactPerson type="QString">3liz</WMSContactPerson>
          <WMSContactPhone type="QString">+334 67 16 64 51</WMSContactPhone>
          <WMSContactPosition type="QString"></WMSContactPosition>
          <WMSExtent type="QStringList">
            <value>417006.61373760335845873</value>
            <value>5394910.34090302512049675</value>
            <value>447158.04891100589884445</value>
            <value>5414844.99480544030666351</value>
          </WMSExtent>
          <WMSKeywordList type="QStringList">
            <value></value>
          </WMSKeywordList>
          <WMSOnlineResource type="QString">http://www.3liz.com/lizmap.html</WMSOnlineResource>
          <WMSRestrictedComposers type="QStringList">
            <value>Composeur1</value>
          </WMSRestrictedComposers>
          <WMSRestrictedLayers type="QStringList"/>
          <WMSServiceAbstract type="QString">Demo project with bus and tramway lines in Montpellier, France.
Data is licensed under ODbl, OpenStreetMap contributors</WMSServiceAbstract>
          <WMSServiceCapabilities type="bool">true</WMSServiceCapabilities>
          <WMSServiceTitle type="QString">Montpellier - Transports</WMSServiceTitle>
          <WMSUseLayerIDs type="bool">false</WMSUseLayerIDs>
          <WMSAddWktGeometry type="bool">true</WMSAddWktGeometry>
        </properties>
        ';
        $oXml = App\XmlTools::xmlReaderFromString($xmlStr);
        $properties = Qgis\ProjectProperties::fromXmlReader($oXml);

        $data = array(
            'WMSServiceTitle' => 'Montpellier - Transports',
            'WMSServiceAbstract' => 'Demo project with bus and tramway lines in Montpellier, France.
Data is licensed under ODbl, OpenStreetMap contributors',
            'WMSKeywordList' => array(''),
            'WMSExtent' => array('417006.61373760335845873', '5394910.34090302512049675', '447158.04891100589884445', '5414844.99480544030666351'),
            'WMSOnlineResource' => 'http://www.3liz.com/lizmap.html',
            'WMSContactMail' => 'info@3liz.com',
            'WMSContactOrganization' => '3liz',
            'WMSContactPerson' => '3liz',
            'WMSContactPhone' => '+334 67 16 64 51',
            'WMSRestrictedComposers' => array('Composeur1'),
            'WMSRestrictedLayers' => array(),
            'WFSLayers' => null,
            'WMSUseLayerIDs' => false,
            'WMSAddWktGeometry' => true,
        );
        foreach($data as $prop => $value) {
            $this->assertEquals($value, $properties->$prop, $prop);
        }*/
        $xml_path = __DIR__.'/../../Project/Ressources/montpellier.qgs';
        // Open the document with XML Reader at the root element document
        $oXml = App\XmlTools::xmlReaderFromFile($xml_path);
        $project = Qgis\ProjectInfo::fromXmlReader($oXml);

        $data = array(
          'version' => '3.10.5-A Coruña',
          'projectname' => 'Montpellier - Transports',
          'saveDateTime' => '',
          'title' => 'Montpellier - Transports',
        );
        foreach($data as $prop => $value) {
            $this->assertEquals($value, $project->$prop, $prop);
        }

        $this->assertNotNull($project->properties);
        $data = array(
          'WMSServiceTitle' => 'Montpellier - Transports',
          'WMSServiceAbstract' => 'Demo project with bus and tramway lines in Montpellier, France.
Data is licensed under ODbl, OpenStreetMap contributors',
          'WMSKeywordList' => array(''),
          'WMSExtent' => array('417006.61373760335845873', '5394910.34090302512049675', '447158.04891100589884445', '5414844.99480544030666351'),
          'WMSOnlineResource' => 'http://www.3liz.com/lizmap.html',
          'WMSContactMail' => 'info@3liz.com',
          'WMSContactOrganization' => '3liz',
          'WMSContactPerson' => '3liz',
          'WMSContactPhone' => '+334 67 16 64 51',
          'WMSRestrictedComposers' => array('Composeur1'),
          'WMSRestrictedLayers' => array(),
          'WMSUseLayerIDs' => false,
        );
        foreach($data as $prop => $value) {
            $this->assertEquals($value, $project->properties->$prop, $prop);
        }

        $this->assertNotNull($project->projectCrs);
        $data = array(
          'proj4' => '+proj=merc +a=6378137 +b=6378137 +lat_ts=0.0 +lon_0=0.0 +x_0=0.0 +y_0=0 +k=1.0 +units=m +nadgrids=@null +wktext  +no_defs',
          'srid' => 0,
          'authid' => 'USER:100000',
          'description' => ' * SCR généré (+proj=merc +a=6378137 +b=6378137 +lat_ts=0.0 +lon_0=0.0 +x_0=0.0 +y_0=0 +k=1.0 +units=m +nadgrids=@null +wktext  +no_defs)',
        );
        foreach($data as $prop => $value) {
            $this->assertEquals($value, $project->projectCrs->$prop, $prop);
        }

        $this->assertNotNull($project->{'layer-tree-group'});
        $this->assertInstanceOf(Qgis\LayerTreeRoot::class, $project->{'layer-tree-group'});
        $this->assertNotNull($project->{'layer-tree-group'}->customOrder);
        $this->assertInstanceOf(Qgis\LayerTreeCustomOrder::class, $project->{'layer-tree-group'}->customOrder);
        $this->assertFalse($project->{'layer-tree-group'}->customOrder->enabled);

        $this->assertNotNull($project->{'visibility-presets'});
        $this->assertTrue(is_array($project->{'visibility-presets'}));
        $this->assertCount(3, $project->{'visibility-presets'});
        $this->assertInstanceOf(Qgis\ProjectVisibilityPreset::class, $project->{'visibility-presets'}[0]);
        $this->assertCount(4, $project->{'visibility-presets'}[0]->layers);
        $this->assertInstanceOf(Qgis\ProjectVisibilityPresetLayer::class, $project->{'visibility-presets'}[0]->layers[0]);
        $data = array(
          'id' => 'SousQuartiers20160121124316563',
          'visible' => True,
          'style' => 'default',
          'expanded' => True,
        );
        foreach($data as $prop => $value) {
          $this->assertEquals($value, $project->{'visibility-presets'}[0]->layers[0]->$prop, $prop);
        }

        $this->assertNotNull($project->relations);
        $this->assertTrue(is_array($project->relations));
        $this->assertCount(7, $project->relations);
        $this->assertInstanceOf(Qgis\ProjectRelation::class, $project->relations[0]);

        $this->assertNotNull($project->projectlayers);
        $this->assertTrue(is_array($project->projectlayers));
        $this->assertCount(18, $project->projectlayers);

        $projections = $project->getProjAsKeyArray();
        $this->assertTrue(is_array($projections));
        $this->assertCount(4, $projections);
        $this->assertArrayHasKey('EPSG:4326', $projections);
        $this->assertArrayHasKey('EPSG:3857', $projections);

        $this->assertNotNull($project->Layouts);
        $this->assertTrue(is_array($project->Layouts));
        $this->assertCount(3, $project->Layouts);
        $this->assertInstanceOf(Qgis\Layout\Layout::class, $project->Layouts[0]);
        $this->assertEquals('Composeur1', $project->Layouts[0]->name);
        $this->assertTrue(is_array($project->Layouts[0]->PageCollection));
        $this->assertCount(1, $project->Layouts[0]->PageCollection);
        $this->assertInstanceOf(Qgis\Layout\LayoutItemPage::class, $project->Layouts[0]->PageCollection[0]);
        $this->assertTrue(is_array($project->Layouts[0]->Items));
        $this->assertCount(5, $project->Layouts[0]->Items);
        $this->assertEquals(65642, $project->Layouts[0]->Items[0]->type);
        $this->assertEquals(65641, $project->Layouts[0]->Items[1]->type);
        $this->assertEquals(65640, $project->Layouts[0]->Items[2]->type);
        $this->assertEquals(65640, $project->Layouts[0]->Items[3]->type);
        $this->assertEquals(65639, $project->Layouts[0]->Items[4]->type);
    }
}
