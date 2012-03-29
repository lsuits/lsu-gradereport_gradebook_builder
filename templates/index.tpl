<div class="container">
  <div class="row">
    <div class="span4" id="grade-categories">
      <table class="table table-bordered table-striped" name="Homework">
        <thead>
          <tr>
            <th>
              <h3>
                <span>Homework</span>
                <span class="label label-important remove-category-label">X &nbsp;Remove</span>
              </h3>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <span>Homework 1
                <span class="label label-important remove-item-label">X &nbsp;Remove</span>
              </span>
              <span class="badge pull-right">10 Points</span>
            </td>
          </tr>
          <tr>
            <td>
              <span>Homework 2
                <span class="label label-important remove-item-label">X &nbsp;Remove</span>
              </span>
              <span class="badge pull-right">10 Points</span>
            </td>
          </tr>
          <tr>
            <td>
              <span>Homework 3
                <span class="label label-important remove-item-label">X &nbsp;Remove</span>
              </span>
              <div class="input-append point-blank pull-right">
                <input class="input-tiny"/>
                <span class="add-on">Points</span>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <table class="table table-bordered table-striped" name="Quiz">
        <thead>
          <tr>
            <th>
              <h3>
                <span>Quiz</span>
                <span class="label label-important remove-category-label">X &nbsp;Remove</span>
              </h3>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <span>Quiz 1
                <span class="label label-important remove-item-label">X &nbsp;Remove</span>
              </span>
              <span class="badge pull-right">10 Points</span>
            </td>
          </tr>
          <tr>
            <td>
              <span>Quiz 2
                <span class="label label-important remove-item-label">X &nbsp;Remove</span>
              </span>
              <span class="badge pull-right">10 Points</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="span8">
      <form class="well form-inline">
        <h3>Add A Grade Category</h3>

        <br />

        <input type="text" class="input-medium" id="category-name" placeholder="Category Name">
        &nbsp;
        &nbsp;
        <button type="submit" class="btn btn-primary" id="add-category">
          Add
        </button>
      </form>

      <form class="well form-inline">
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
          <option>Homework</option>
          <option>Quiz</option>
        </select>
        &nbsp;
        &nbsp;
        <button type="submit" class="btn btn-primary" id="add-item">
          Add
        </button>
      </form>

      <div class="well">
        <h3>Grading Method</h3>

        <br />

        <select class="input-medium" id="grading-method">
          <option>Weight By Points</option>
          <option>Custom Weights</option>
          <option>Sum of Grades</option>
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

      <button class="btn btn-large btn-primary save-button">Save to Gradebook</button>
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
