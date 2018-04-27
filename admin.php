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
            $identifier = $row["identifier"];
            echo "<option value=\"$identifier\">$identifier</option>";
          }
        ?>
      </select>
      <label>Session</label>
      <a class="waves-effect waves-light btn" id="admin-session-refresh">Refresh</a>
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
          $identifier = $row["identifier"];
          $password = $row["password"];
          $start = $row["start"];
          $end = $row["end"];
          echo "<tr> <td>$identifier</td> <td>$password</td> <td>$start</td> <td>$end</td> </tr>";
        }
      ?>

    </tbody>
  </table>
</div>
