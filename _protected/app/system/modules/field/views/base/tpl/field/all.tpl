<div class="middle">
    {if !empty($fields)}
        <table class="table">
            <thead>
                <tr>
                    <th>{lang 'Name'}</th>
                    <th>{lang 'Edit'}</th>
                    <th>{lang 'Delete'}</th>
                </tr>
            </thead>

            <tbody>
                {each $field in $fields}
                    {{ $unmodifiable = Field::unmodifiable($mod, $field) }}
                    <tr>
                        <td>{% $field %}</td>
                        <td>
                            {if !$unmodifiable}
                                <a href="{{ $design->url('field','field','edit',"$mod,$field") }}">{lang 'Edit'}</a>
                            {else}
                                <span class="gray">{lang 'Not editable'}</span>
                            {/if}
                        </td>
                        <td>
                            {if !$unmodifiable}
                                {{ LinkCoreForm::display(t('Delete'), 'field', 'field', 'delete', array('mod'=>$mod, 'name'=>$field)) }}
                            {else}
                                <span class="gray">{lang 'Not deletable'}</span>
                            {/if}
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>

        <script>$('table tr td input[type=submit]').click(function() {
            return confirm('{lang 'Warning! This action will remove a User Field! (Irreversible Action)'}');
        });</script>
    {else}
        <p class="bold">{lang 'To see the users fields, you must add at least one user.'}</p>
    {/if}

    <p class="center"><a class="btn btn-default btn-md" href="{{ $design->url('field','field','add',$mod) }}">{lang 'Add a Field'}</a></p>
</div>
