<tbody>
{foreach $nodes as $node}
    <tr rel="{$node.uri}" parent="{$parent}" data-parent="true">
        <td>{$node.name}{if $debuging}({$node.resId}){/if}</td>
        {if $node.defaultOp}
            <td class="text-center">
                <input type="radio" name="acl[{$node.resId}]" {if $acl[$node.resId] == '1'}checked="checked"{/if}
                       value="1"/>
            </td>
            <td class="text-center">
                <input type="radio" name="acl[{$node.resId}]" {if !isset($acl[$node.resId])}checked="checked"{/if}
                       value=""/>
            </td>
            <td class="text-center">
                <input type="radio" name="acl[{$node.resId}]" {if $acl[$node.resId] == '0'}checked="checked"{/if}
                       value="0"/>
            </td>
        {else}
            <td></td>
            <td></td>
            <td></td>
        {/if}
    </tr>
{/foreach}
{foreach $ops as $o=>$n}
    <tr rel="" parent="{$parent}">
        <td>{$n.name}{if $debuging}({$n.resId}){/if}</td>
        <td class="text-center">
            <input type="radio" name="acl[{$n.resId}]" {if $acl[$n.resId] == '1'}checked="checked"{/if} value="1"/>
        </td>
        <td class="text-center">
            <input type="radio" name="acl[{$n.resId}]" {if !isset($acl[$n.resId])}checked="checked"{/if} value=""/>
        </td>
        <td class="text-center">
            <input type="radio" name="acl[{$n.resId}]" {if $acl[$n.resId] == '0'}checked="checked"{/if} value="0"/>
        </td>
    </tr>
{/foreach}
</tbody>