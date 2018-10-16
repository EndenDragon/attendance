<br><hr>
<h2>Admin</h2>
<div class="row">
  <div class="col s12">
    <p class="flow-text">Signed in members</p>
  </div>
  <div class="col s12">
    <div class="input-field col s12 m6">
      <select id="admin-session-select">
        <option value="" disabled selected>Choose your session</option>
        <?php
          $sql_query = "SELECT identifier FROM sessions;";
          $result_set = $database->query($sql_query);
          foreach ($result_set as $row) {
            $identifier = htmlspecialchars($row["identifier"]);
            echo "<option value=\"$identifier\">$identifier</option>";
          }
        ?>
      </select>
      <label>Session</label>
      <a class="waves-effect waves-light btn" id="admin-session-refresh">Refresh</a>
      <a class="waves-effect waves-light btn" id="admin-session-canvas-submit" disabled>Submit to Canvas</a>
    </div>
  </div>
  <div class="col s12">
    <table>
      <thead>
        <tr>
          <th>DB ID</th>
          <th>UWNetID</th>
          <th>Timestamp</th>
        </tr>
      </thead>
      <tbody id="admin-members-table"></tbody>
    </table>
    <p>Count: <span id="admin-session-member-count"></span></p>
  </div>
  <div class="col s12">
    <p class="flow-text">Add or edit sessions</p>
  </div>
  <div class="col s12">
    <div class="input-field col s12 m6">
      <input id="admin-name-field" type="text">
      <label for="admin-name-field">Session Name</label>
    </div>
    <div class="input-field col s12 m6">
      <input id="admin-password-field" type="text">
      <label for="admin-password-field">Password</label>
    </div>
    <div class="col s12 m3">
      <label for="admin-datepicker-start">Start Date</label>
      <input type="text" class="datepicker" id="admin-datepicker-start">
    </div>
    <div class="col s12 m3">
      <label for="admin-timepicker-start">Start Time</label>
      <input type="text" class="timepicker" id="admin-timepicker-start">
    </div>
    <div class="col s12 m3">
      <label for="admin-datepicker-end">End Date</label>
      <input type="text" class="datepicker" id="admin-datepicker-end">
    </div>
    <div class="col s12 m3">
      <label for="admin-timepicker-end">End Time</label>
      <input type="text" class="timepicker" id="admin-timepicker-end">
    </div>
    <div class="col s12">
      <a class="waves-effect waves-light btn-large" id="admin-update-session-btn">Add/Edit Session</a>
    </div>
  </div>
</div>
<div class="col s12">
  <p class="flow-text">Sessions List</p>
</div>
<div class="col s12">
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Password</th>
        <th>Start</th>
        <th>End</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $sql_query = "SELECT identifier, password, start, end FROM sessions;";
        $result_set = $database->query($sql_query);
        foreach ($result_set as $row) {
          $identifier = htmlspecialchars($row["identifier"]);
          $password = htmlspecialchars($row["password"]);
          $start = htmlspecialchars($row["start"]);
          $end = htmlspecialchars($row["end"]);
          echo "<tr> <td>$identifier</td> <td>$password</td> <td>$start</td> <td>$end</td> </tr>";
        }
      ?>

    </tbody>
  </table>
</div>


<div id="canvas-submit-modal" class="modal modal-fixed-footer">
  <div class="modal-content">
    <h4>Submit to Canvas: <span id="canvas-submit-sessionname"></span></h4>
    <p class="flow-text">Submitting to <strong id="canvas-submit-coursename"></strong></p>
    <div class="input-field col s12 m6">
      <select id="canvas-submit-assignments-list">
        <option value="" disabled selected>Choose an assignment</option>
      </select>
    </div>
    <p id="loading-course-msg" class="red-text">* Loading Course Assignments, please wait...</p>
    <p>Populate additional students from a chosen section:</p>
    <div class="input-field col s12 m6">
      <select id="canvas-submit-sections-list">
        <option value="" selected>No section</option>
      </select>
    </div>
    <p id="loading-section-msg" class="red-text">* Loading Course Sections, please wait...</p>
    <div>
      <p class="flow-text">Manage Grades for the assignment</p>
      <table>
      	<thead>
      		<tr>
      			<td>Default Grade</td>
      			<td>Pass/Fail</td>
      			<td>Numerical</td>
      		</tr>
      	</thead>
      	<tbody>
      		<tr>
      			<td>Signed in Members</td>
				<td>
				  <div class="switch">
				      <label>
				          Incomplete
				          <input type="checkbox" checked=checked id="admin_canvas_default_passfail">
				          <span class="lever"></span> Complete
				      </label>
				  </div>
				</td>
				<td>
				  <div class="input-field inline">
				      <input placeholder="Grade" value="2" id="admin_canvas_default_grade">
				  </div>
				</td>
      		</tr>
      		<tr>
      			<td>Section Members not signed in</td>
				<td>
				  <div class="switch">
				      <label>
				          Incomplete
				          <input type="checkbox" id="admin_canvas_default_unlogged_passfail">
				          <span class="lever"></span> Complete
				      </label>
				  </div>
				</td>
				<td>
				  <div class="input-field inline">
				      <input placeholder="Grade" value="0" id="admin_canvas_default_unlogged_grade">
				  </div>
				</td>
      		</tr>
      	</tbody>
      </table>
      <a class="waves-effect waves-light btn" id="admin_canvas_default_refresh">Use Defaults</a>
      <table class="bordered striped">
          <thead>
              <tr>
                  <th>UWNetID</th>
                  <th>Grade</th>
              </tr>
          </thead>
          <tbody id="canvas-submit-members-table">
          </tbody>
      </table>
      <hr>
      <a class="waves-effect waves-light btn" id="canvas-submit-send" disabled>Send to Canvas</a>
    </div>
  </div>
  <div class="modal-footer">
    <a href="#" class="modal-action modal-close waves-effect waves-red btn-flat ">Close</a>
  </div>
</div>
