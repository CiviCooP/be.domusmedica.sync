<h3>Resultaten van het laden van het Permaned bestand</h3>

{if $failures}
    <table>
        <tr>
            <th>id</th>
            <th>message</th>
        </tr>
        {foreach from=$failures item=row}
            <tr>
                <td>{$row.id}</td>
                <td>{$row.message}</td>
            </tr>
        {/foreach}
    </table>


{/if}
