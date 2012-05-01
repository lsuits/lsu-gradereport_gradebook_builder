<div class="container" id="builder-start">
  <div class="row">
    <div class="span4">
        {select templates=$templates}
        <h3 id="template-name">
            <span id="template-toggle-input">{$template->name}</span>
        </h3>
    </div>
  </div>
  <div class="row">
    <div class="span4" id="grade-categories">
    </div>
    <div class="span8">
        <h3>Add A Grade Category</h3>
        <input type="text" class="input-medium" id="category-name" placeholder="Category Name">
        &nbsp;
        <button type="submit" class="btn btn-primary" id="add-category">
          Add
        </button>
      </form>
      <form id="add-items" class="well form-inline">
        <h3>Add Grade Item(s)</h3>
        <div class="nowrap">
            <input type="text" class="input-tiny" id="grade-item-num-add" value="1">
            &nbsp;
            <select id="grade-itemtype" class="input-medium">
              {foreach $grade_options as $type => $display}
              <option value="{$type}">{$display}</option>
              {/foreach}
            </select>
            &nbsp;
            to
            &nbsp;
            <select class="input-medium" id="add-item-category">
            </select>
            &nbsp;
            <button type="submit" class="btn btn-primary" id="add-item">
              Add
            </button>
        </div>
      </form>
      <div class="well">
        <h3>Grading Method</h3>
        <select class="input-large" id="grading-method">
          {foreach $aggregations as $value => $name}
            <option value="{$value}">{$name}</option>
          {/foreach}
        </select>
        <form class="form-horizontal" id="category-weights">
          <h3>Category Weights</h3>
          <fieldset>
          </fieldset>
        </form>
      </div>
      <form method="post" id="builder">
        <input type="hidden" name="id" value="{$courseid}"/>
        <input type="hidden" name="name" value="{$template->name}"/>
        <input type="hidden" name="data" value="{$template->data|escape}"/>
        <input type="hidden" name="contextlevel" value="{$template->contextlevel}"/>
        {if !empty($template->id) }
        <input type="hidden" name="template" value="{$template->id}"/>
        {/if}
        <button class="btn btn-large btn-primary" type="submit" id="save-button">Save to Gradebook</button>
      </form>
    </div>
  </div>
</div>
<div id="grade-category-tmpl">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>
          <h3>
            <span></span>
            <span class="label label-important remove remove-category-label">X</span>
          </h3>
        </th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</div>
<div id="grade-item-tmpl">
  <table>
    <tr>
      <td>
        <span>
          <span class="label label-important remove remove-item-label">X</span>
        </span>
        <div class="input-append point-blank pull-right">
          <input class="input-tiny"/>
          <span class="add-on">Points</span>
        </div>
      </td>
    </tr>
  </table>
</div>
<div id="category-weight-tmpl">
  <div class="control-group">
    <label class="control-label"><span></span></label>
    <div class="controls">
      <div class="input-append">
        <input type="text" class="input-tiny" value="0"/>
        <span class="add-on">%</span>
      </div>
    </div>
  </div>
</div>
