{UIBROWSER->getMDataArr id=$i.id assign='_metaarr'}
                          
onMouseover = "showalttext('<div style=&quot;font-size: 120%; font-weight: bold&quot;>##{$i.type|lower|capitalize}##: {$_metaarr.metadata.Title}</div>' +
                            {if $i.type|lower == 'playlist'}
                                {if $PL->isUSedBy($i.id) != false}'<div>##(used by {$PL->isUSedBy($i.id)})##</div>' + {/if}

                                {foreach from=$PL->getFlat($i.id) item='_pli'}
                                    '<div>{$_pli.title} &nbsp;{$_pli.duration|truncate:8:''}</div>' +
                                {/foreach}
                            {/if}
                           '{foreach from=$_metaarr.metadata key='_key' item='_item'}{if $_key != 'Title'}<div>{$_key}: {$_item}</div>{/if}{/foreach}')"
onMouseout  = "hidealttext()"

{assign var='_metaarr' value=null}
