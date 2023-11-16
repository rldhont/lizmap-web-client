<?php

use PHPUnit\Framework\TestCase;
use Lizmap\Project\Qgis;
use Lizmap\App;

/**
 * @internal
 * @coversNothing
 */
class LayerTreeRootTest extends TestCase
{
    public function testConstruct()
    {
        $data = array(
          'customOrder' => new Qgis\LayerTreeCustomOrder(array(
            'enabled' => False,
          )),
        );

        $root = new Qgis\LayerTreeRoot($data);
        $this->assertInstanceOf(Qgis\LayerTreeCustomOrder::class, $root->customOrder);
        $this->assertFalse($root->customOrder->enabled);
        $this->assertEquals(array(), $root->customOrder->items);

        $items = array(
            'A',
            'B',
            'C',
        );
        $data = array(
            'customOrder' => new Qgis\LayerTreeCustomOrder(array(
              'enabled' => True,
              'items' => $items,
            )),
        );
        $root = new Qgis\LayerTreeRoot($data);
        $this->assertInstanceOf(Qgis\LayerTreeCustomOrder::class, $root->customOrder);
        $this->assertTrue($root->customOrder->enabled);
        $this->assertEquals($items, $root->customOrder->items);

        $items = array(
            'A',
            'B',
            'C',
        );
        $data = array(
            'customOrder' => new Qgis\LayerTreeCustomOrder(array(
              'enabled' => False,
              'items' => $items,
            )),
        );
        $root = new Qgis\LayerTreeRoot($data);
        $this->assertInstanceOf(Qgis\LayerTreeCustomOrder::class, $root->customOrder);
        $this->assertFalse($root->customOrder->enabled);
        $this->assertEquals(array(), $root->customOrder->items);
    }

    public function testFromXmlReader()
    {
        $xmlStr = '
      <layer-tree-group>
        <custom-order enabled="0">
          <item>edition_point20130118171631518</item>
          <item>edition_line20130409161630329</item>
          <item>edition_polygon20130409114333776</item>
          <item>bus_stops20121106170806413</item>
          <item>bus20121102133611751</item>
          <item>VilleMTP_MTP_Quartiers_2011_432620130116112610876</item>
          <item>VilleMTP_MTP_Quartiers_2011_432620130116112351546</item>
          <item>tramstop20150328114203878</item>
          <item>tramway20150328114206278</item>
          <item>publicbuildings20150420100958543</item>
          <item>SousQuartiers20160121124316563</item>
          <item>osm_stamen_toner20180315181710198</item>
          <item>osm_mapnik20180315181738526</item>
        </custom-order>
      </layer-tree-group>
        ';
        $oXml = App\XmlTools::xmlReaderFromString($xmlStr);
        $root = Qgis\LayerTreeRoot::fromXmlReader($oXml);

        $items = array();

        $this->assertInstanceOf(Qgis\LayerTreeCustomOrder::class, $root->customOrder);
        $this->assertFalse($root->customOrder->enabled);
        $this->assertEquals($items, $root->customOrder->items);

        $xmlStr = '
      <layer-tree-group>
        <custom-order enabled="1">
          <item>edition_point20130118171631518</item>
          <item>edition_line20130409161630329</item>
          <item>edition_polygon20130409114333776</item>
          <item>bus_stops20121106170806413</item>
          <item>bus20121102133611751</item>
          <item>VilleMTP_MTP_Quartiers_2011_432620130116112610876</item>
          <item>VilleMTP_MTP_Quartiers_2011_432620130116112351546</item>
          <item>tramstop20150328114203878</item>
          <item>tramway20150328114206278</item>
          <item>publicbuildings20150420100958543</item>
          <item>SousQuartiers20160121124316563</item>
          <item>osm_stamen_toner20180315181710198</item>
          <item>osm_mapnik20180315181738526</item>
        </custom-order>
      </layer-tree-group>
        ';
        $oXml = App\XmlTools::xmlReaderFromString($xmlStr);
        $root = Qgis\LayerTreeRoot::fromXmlReader($oXml);

        $items = array(
            'edition_point20130118171631518',
            'edition_line20130409161630329',
            'edition_polygon20130409114333776',
            'bus_stops20121106170806413',
            'bus20121102133611751',
            'VilleMTP_MTP_Quartiers_2011_432620130116112610876',
            'VilleMTP_MTP_Quartiers_2011_432620130116112351546',
            'tramstop20150328114203878',
            'tramway20150328114206278',
            'publicbuildings20150420100958543',
            'SousQuartiers20160121124316563',
            'osm_stamen_toner20180315181710198',
            'osm_mapnik20180315181738526',
        );

        $this->assertInstanceOf(Qgis\LayerTreeCustomOrder::class, $root->customOrder);
        $this->assertTrue($root->customOrder->enabled);
        $this->assertEquals($items, $root->customOrder->items);
    }
}
