<div class="container" id="builder-start">
  <div class="row">
    <div class="span12">
        {select templates=$templates}
    </div>
  </div>
  <div class="row">
    <div class="span4" id="grade-categories">
    </div>

    <div class="span8">
      <form class="well form-inline">
        <h3>Add A Grade Category</h3>

        <br />

        <input type="text" class="input-medium" id="category-name" placeholder="Category Name">
        &nbsp;
        <button type="submit" class="btn btn-primary" id="add-category">
          Add
        </button>
      </form>

      <form id="add-items" class="well form-inline">
        <h3>Add Grade Item(s)</h3>

        <br />

        <input type="text" class="input-tiny" id="grade-item-num-add" value="1">
        &nbsp;
        <select class="input-medium">
          <option>Normal Grade</option>
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
      </form>

      <div class="well">
        <h3>Grading Method</h3>

        <br />

        <select class="input-large" id="grading-method">
          {foreach $aggregations as $value => $name}
            <option value="{$value}">{$name}</option>
          {/foreach}
        </select>

        <form class="form-horizontal" id="category-weights">
          <br />

          <h3>Category Weights</h3>

          <br />

          <fieldset>
            <div class="control-group" name="Homework">
              <label class="control-label"><span>Homework</span></label>
              <div class="controls">
                <div class="input-append">
                  <input type="text" class="input-tiny" value="10"/>
                  <span class="add-on">%</span>
                </div>
              </div>
            </div>

            <div class="control-group" name="Quiz">
              <label class="control-label"><span>Quiz</span></label>
              <div class="controls">
                <div class="input-append">
                  <input type="text" class="input-tiny" value="5"/>
                  <span class="add-on">%</span>
                </div>
              </div>
            </div>
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
            <span class="label label-important remove-category-label">X &nbsp;Remove</span>
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
          <span class="label label-important remove-item-label">X &nbsp;Remove</span>
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
        <input type="text" class="input-tiny" value="5"/>
        <span class="add-on">%</span>
      </div>
    </div>
  </div>
</div>
