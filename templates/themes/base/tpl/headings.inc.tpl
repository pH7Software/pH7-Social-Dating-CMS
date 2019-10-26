<div class="center" id="headings">
  {each $heading => $headingVar in ['h1' => 'h1_title', 'h2' => 'h2_title', 'h3' => 'h3_title', 'h4' => 'h4_title']}

    {* Two dollar signs to use the variable value and treat it as the variable name.
       http://php.net/manual/en/language.variables.variable.php *}
    {if !empty($$headingVar)}
      <{heading}>{% $$headingVar %}</{heading}>
    {/if}
  {/each}
</div>
