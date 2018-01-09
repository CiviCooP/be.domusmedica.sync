<h3>Resultaten van het laden van het Permaned bestand</h3>

{if $failures}
    <table>
        <tr>
            <th>id</th>
            <th>Name</th>

            <th>message</th>
        </tr>
        {foreach from=$failures item=row}
            <tr>
                <td>{$row.id}</td>
                {if $row.contact_id}
                    {assign var='cid' value=$row.contact_id}
                    <td><a href='{crmURL p="civicrm/contact/view" q="reset=1&cid=$cid"}' >{$row.naam}</a></td>
                {else}
                <td>{$row.naam}</td>
                {/if}

                <td>{$row.message}</td>
            </tr>
        {/foreach}
    </table>
{/if}
