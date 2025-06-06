{include 'lizmap~copy_to_clipboard'}

    <div>
      <div>
        <p>
          <img
                  src="{jurl 'view~media:illustration', array('repository'=>$repository,'project'=>$project)}"
                  alt="project image"
                  class="img-polaroid liz-project-img"
                  width="200"
                  height="200"
                  loading="lazy"
          >

          <dl class="dl-vertical">
            {if $WMSServiceTitle}
            <dt>{@view~map.metadata.description.title@}</dt>
            <dd>{$WMSServiceTitle}&nbsp;</dd>
            <br/>
            {/if}

            {if $WMSServiceAbstract}
            <dt>{@view~map.metadata.description.abstract@}</dt>
            <dd>{$WMSServiceAbstract|nl2br}&nbsp;</dd>
            <br/>
            {/if}

            {if $WMSContactOrganization}
            <dt>{@view~map.metadata.contact.organization@}</dt>
            <dd>{$WMSContactOrganization}&nbsp;</dd>
            <br/>
            {/if}

            {if $WMSContactPerson}
            <dt>{@view~map.metadata.contact.person@}</dt>
            <dd>{$WMSContactPerson}&nbsp;</dd>
            <br/>
            {/if}

            {if $WMSContactMail}
            <dt>{@view~map.metadata.contact.email@}</dt>
            <dd>{$WMSContactMail|replace:'@':' (at) '}&nbsp;</dd>
            <br/>
            {/if}

            {if $WMSContactPhone}
            <dt>{@view~map.metadata.contact.phone@}</dt>
            <dd>{$WMSContactPhone}&nbsp;</dd>
            <br/>
            {/if}

            {if $WMSOnlineResource}
            <dt>{@view~map.metadata.resources.website@}</dt>
            <dd><a href="{$WMSOnlineResource}" target="_blank">{$WMSOnlineResource}</a></dd>
            <br/>
            {/if}

            <dt>{@view~map.metadata.properties.projection@}</dt>
            <dd><small class="proj">{$ProjectCrs}&nbsp;</small></dd>
            <br/>
            <dt>{@view~map.metadata.properties.extent@}</dt>
            <dd><small class="bbox">{$WMSExtent}</small></dd>
            <br/>

            {ifacl2 'lizmap.tools.displayGetCapabilitiesLinks', $repository}
            {if $wmsGetCapabilitiesUrl}
            <dt>{@view~map.metadata.properties.wmsGetCapabilitiesUrl@}</dt>
            <dd>
              <small>
                <a href="{$wmsGetCapabilitiesUrl}" target="_blank">WMS URL</a>
              </small>
              {usemacro 'copy_to_clipboard', $wmsGetCapabilitiesUrl}
            </dd>
            <dd>
              <small>
                <a id="metadata-wmts-getcapabilities-url" href="{$wmtsGetCapabilitiesUrl}" target="_blank">WMTS URL</a>
              </small>
              {usemacro 'copy_to_clipboard', $wmtsGetCapabilitiesUrl}
            </dd>
            {ifacl2 'lizmap.tools.layer.export', $repository}
              <dd>
                <small>
                  <a href="{$wfsGetCapabilitiesUrl}" target="_blank">WFS URL</a>
                </small>
                {usemacro 'copy_to_clipboard', $wfsGetCapabilitiesUrl}
              </dd>
            {/ifacl2}
            <br/>
            {/if}
            {/ifacl2}
          </dl>
        </p>
      </div>
    </div>
