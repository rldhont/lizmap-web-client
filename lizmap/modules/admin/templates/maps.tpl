{jmessage_bootstrap}

<h1>{@admin~admin.configuration.repository.label@}</h1>

{ifacl2 'lizmap.admin.repositories.view'}
    <!--Repositories-->
    <div class="admin_repositories">

        <!--Add a repository-->
        {ifacl2 'lizmap.admin.repositories.create'}
            <div style="margin:20px 0px;">
                <a class="btn" href="{jurl 'admin~maps:createSection'}">{@admin~admin.configuration.button.add.repository.label@}</a>
            </div>
        {/ifacl2}

        <!--Loop on repositories-->
        {foreach $repositories as $repo}

        <div id="{$repo->getKey()}" class="admin_repository">
            <legend>{$repo->getKey()}
            {if !$repo->hasValidPath() }
                <span class='badge bg-warning'>{@admin~admin.form.admin_section.repository.path.invalid@}</span>
            {/if}
            </legend>
            <dl><dt>{@admin~admin.form.admin_section.data.label@}</dt>
                <dd>
                    <table class="table">
                        {assign $section = 'repository:'.$repo->getKey()}
                        {assign $properties = $repo->getProperties()}
                        {foreach $properties as $prop}
                            <tr>
                                {if $prop == 'path' && $rootRepositories != ''}
                                    {if substr($repo->getPath(), 0, strlen($rootRepositories)) === $rootRepositories}
                                        {assign $d = substr($repo->getPath(), strlen($rootRepositories))}
                                        <th>{@admin~admin.form.admin_section.repository.$prop.label@}</th><td>{$d}</td>
                                    {/if}
                                {else}
                                    {* FIXME don't use getData() which is deprecated *}
                                    {assign $value = $repo->getData($prop)}
                                    {if $prop == 'allowUserDefinedThemes'}
                                        {if $value == 1}
                                            {assign $value = '✔️'}
                                        {else}
                                            {assign $value = '-'}
                                        {/if}
                                    {/if}
                                <th>{@admin~admin.form.admin_section.repository.$prop.label@}</th><td>{$value}</td>
                                {/if}
                            </tr>
                        {/foreach}
                    </table>
                </dd>
            </dl>

            <dl><dt>{@admin~admin.form.admin_section.groups.label@}</dt>
                <dd>
                    <table class="table">
                        {foreach $subjects as $s}
                            {if property_exists($data[$repo->getKey()], $s)}
                                <tr>
                                    <th>{$labels[$s]}</th><td>{$data[$repo->getKey()]->$s}</td>
                                </tr>
                            {/if}
                        {/foreach}
                    </table>
                </dd>
            </dl>

            <div class="form-actions">
                <!-- View repository page -->
                {ifacl2 'lizmap.repositories.view', $repo->getKey()}
                    <a
                        class="btn"
                        href="{jurl 'view~default:index', array('repository'=>$repo->getKey())}"
                        target="_blank"
                        title="{@admin~admin.configuration.button.view.repository.tooltip@}"
                    >{@admin~admin.configuration.button.view.repository.label@}</a>
                {/ifacl2}
                <!-- Modify -->
                {ifacl2 'lizmap.admin.repositories.update'}
                    <a
                        class="btn"
                        href="{jurl 'admin~maps:modifySection', array('repository'=>$repo->getKey())}"
                        title="{@admin~admin.configuration.button.modify.repository.tooltip@}"
                    >{@admin~admin.configuration.button.modify.repository.label@}</a>
                {/ifacl2}
                <!-- Remove -->
                {ifacl2 'lizmap.admin.repositories.delete'}
                    <a
                        class="btn"
                        href="{jurl 'admin~maps:removeSection', array('repository'=>$repo->getKey())}"
                        onclick="return confirm(`{@admin~admin.configuration.button.remove.repository.confirm.label@}`)"
                        title="{@admin~admin.configuration.button.remove.repository.tooltip@}"
                    >{@admin~admin.configuration.button.remove.repository.label@}</a>
                {/ifacl2}
                {ifacl2 'lizmap.admin.repositories.delete'}
                    <a
                        class="btn"
                        href="{jurl 'admin~maps:removeCache', array('repository'=>$repo->getKey())}"
                        onclick="return confirm(`{@admin~admin.cache.button.remove.repository.cache.confirm.label@}`)"
                        title="{@admin~admin.cache.button.remove.repository.cache.tooltip@}"
                    >{@admin~admin.cache.button.remove.repository.cache.label@}</a>
                {/ifacl2}
            </div>
        </div>

        {/foreach}

    </div>
{/ifacl2}

<!--Add a repository-->
{if count($repositories)}
    {ifacl2 'lizmap.admin.repositories.create'}
        <a class="btn" href="{jurl 'admin~maps:createSection'}">{@admin~admin.configuration.button.add.repository.label@}</a>
    {/ifacl2}
{/if}
