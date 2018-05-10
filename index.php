<?php
  include("config.php");
?>

<!DOCTYPE html>
<html>
  <head>
    <!--Import Google Icon Font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css"  media="screen,projection"/>

    <style>
      .brand-logo {
        margin-left: 10px;
      }

      h1 {
        font-size: 30pt;
      }

      h2 {
        font-size: 25pt;
      }

      .btn-large {
        width: 100%;
      }

      body {
        display: flex;
        min-height: 100vh;
        flex-direction: column;
      }

      main {
        width: 90%;
        margin-left: auto;
        margin-right: auto;
        flex: 1 0 auto;
      }
    </style>

    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?=$course_title?> Attendance</title>
  </head>

  <body>
    <nav>
      <div class="nav-wrapper">
        <a href="#" class="brand-logo"><?=$course_title?> Attendance</a>
      </div>
    </nav>

    <main>
      <h1>Welcome <strong><?=$_SERVER["REMOTE_USER"]?></strong>,<br>Select a session, enter the password, and hit <em>SIGN ME IN</em>!</h1>
      <div class="row">
        <div class="col s12">

          <div class="input-field col s12 m6">
            <select id="session-select">
              <option value="" disabled selected>Choose your session</option>
              <?php
                $sql_query = "SELECT identifier, start, end FROM sessions;";
                $result_set = $database->query($sql_query);
                foreach ($result_set as $row) {
                  $start = $row["start"];
                  $end = $row["end"];
                  $now = date('Y-m-d H:i:s');
                  $identifier = $row["identifier"];
                  if ($start <= $now && $now <= $end) {
                    echo "<option value=\"$identifier\">$identifier</option>";
                  }
                }
              ?>
            </select>
            <label>Session</label>
          </div>

          <div class="input-field col s12 m6">
            <input id="password-field" type="text">
            <label for="password-field">Password</label>
          </div>

          <div class="col s12">
            <a class="waves-effect waves-light btn-large" id="sign-in-btn">SIGN ME IN</a>
          </div>

        </div>
      </div>

      <?php
        if (in_array($_SERVER["REMOTE_USER"], $admins)) {
          include("admin.php");
        }
      ?>
    </main>

    <footer class="page-footer">
      <div class="footer-copyright">
        <div class="container">
        © 2017 Jeremy Zhang
        </div>
      </div>
    </footer>
    <!--Import jQuery before materialize.js-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/mustache.js/2.3.0/mustache.min.js" integrity="sha256-iaqfO5ue0VbSGcEiQn+OeXxnxAMK2+QgHXIDA5bWtGI=" crossorigin="anonymous"></script>

    <script>
      function signin_submit(session, password) {
          var url = "signin.php";
          var funct = $.ajax({
              dataType: "json",
              url: url,
              method: "POST",
              data: {"session": session, "password": password}
          });
          return funct.promise();
      }

      function sessions_get(session) {
          var url = "sessions.php";
          var funct = $.ajax({
              dataType: "json",
              url: url,
              method: "GET",
              data: {"session": session}
          });
          return funct.promise();
      }

      function canvas_show_course() {
        var url = "canvas.php?action=show_course";
        var funct = $.ajax({
            dataType: "json",
            url: url,
            method: "GET"
        });
        return funct.promise();
      }

      function canvas_list_assignments() {
        var url = "canvas.php?action=list_assignments";
        var funct = $.ajax({
            dataType: "json",
            url: url,
            method: "GET"
        });
        return funct.promise();
      }

      function canvas_list_users() {
        var url = "canvas.php?action=list_users";
        var funct = $.ajax({
            dataType: "json",
            url: url,
            method: "GET"
        });
        return funct.promise();
      }

      function canvas_post_grade(assignment_id, posted_grade) {
          var url = "canvas.php?action=post_grade";
          var funct = $.ajax({
              dataType: "json",
              url: url,
              method: "POST",
              data: {"assignment_id": assignment_id, "posted_grade": JSON.stringify(posted_grade)}
          });
          return funct.promise();
      }

      $(document).ready(function() {
        $('select').material_select();
        $('.modal').modal();
        $('.datepicker').pickadate({
          selectMonths: true, // Creates a dropdown to control month
          selectYears: 15, // Creates a dropdown of 15 years to control year,
          today: 'Today',
          clear: 'Clear',
          close: 'Ok',
          closeOnSelect: false // Close upon selecting a date,
        });
        $('.timepicker').pickatime({
          default: 'now', // Set default time: 'now', '1:30AM', '16:30'
          fromnow: 0,       // set default time to * milliseconds from now (using with default = 'now')
          twelvehour: false, // Use AM/PM or 24-hour format
          donetext: 'OK', // text for done-button
          cleartext: 'Clear', // text for clear-button
          canceltext: 'Cancel', // Text for cancel-button
          autoclose: false, // automatic close timepicker
          ampmclickable: true, // make AM PM clickable
          aftershow: function(){} //Function for after opening timepicker
        });

        $("#sign-in-btn").click(function () {
          var session = $('#session-select').find(":selected").val();
          var password = $("#password-field").val();
          var signin = signin_submit(session, password);
          signin.done(function () {
            Materialize.toast('Successfully signed in!', 10000);
          });
          signin.fail(function (data) {
            Materialize.toast('Error: ' + data.responseJSON.error, 10000);
          });
        });

        const adminEnabled = <?php if (in_array($_SERVER["REMOTE_USER"], $admins)) {echo "true";} else {echo "false";} ?>;

        if (adminEnabled) {
          var signedInNetIDs = [];
          var courseStudents = [];

          $("#admin-update-session-btn").click(function () {
            var name = $("#admin-name-field").val();
            var password = $("#admin-password-field").val();
            var startdate = $("#admin-datepicker-start").val();
            var starttime = $("#admin-timepicker-start").val();
            var enddate = $("#admin-datepicker-end").val();
            var endtime = $("#admin-timepicker-end").val();
            var payload = {
              "name": name,
              "password": password,
              "startdate": startdate,
              "starttime": starttime,
              "enddate": enddate,
              "endtime": endtime,
            }
            $.post("modify.php", payload, function(data) {
              location.reload();
            });
          });

            $("#admin-session-select").change(generateSessionTable);
            $("#admin-session-refresh").click(generateSessionTable);
            $("#canvas-submit-assignments-list").change(generateSubmitCanvasTable);
            $("#canvas-submit-send").click(sendToCanvas);

            function generateSessionTable() {
                var session = $("#admin-session-select option:selected").val();
                var sess = sessions_get(session);
                sess.done(function (data) {
                    $("#admin-session-canvas-submit").attr("disabled", false);
                    var template = $('#mustache_adminMembersTable').html();
                    Mustache.parse(template);
                    $("#admin-members-table").empty();
                    signedInNetIDs = [];
                    for (var i = 0; i < data.length; i++) {
                        var disdata = data[i];
                        var rendered = Mustache.render(template, {"id": disdata.id, "netid": disdata.netid, "timestamp": disdata.timestamp});
                        signedInNetIDs.push(disdata.netid);
                        $("#admin-members-table").append(rendered);
                    }
                    $("#admin-session-member-count").html(data.length);
                });
            }

            function generateSubmitCanvasTable() {
              var assignmentGradingType = $("#canvas-submit-assignments-list option:selected").attr("data-grading-type");
              var assignmentIsPassFail = assignmentGradingType == "pass_fail";
              $("#canvas-submit-send").attr("disabled", false);
              $("#canvas-submit-members-table").html("");
              var template = $('#mustache_adminCanvasMembersTable').html();
              Mustache.parse(template);
              for (var i = 0; i < signedInNetIDs.length; i++) {
                var rendered = Mustache.render(template, {"netid": signedInNetIDs[i], "pass_fail": assignmentIsPassFail});
                $("#canvas-submit-members-table").append(rendered);
              }
            }

            function sendToCanvas() {
              $("#canvas-submit-send").attr("disabled", true);
              var assignmentID = $("#canvas-submit-assignments-list option:selected").val();
              var trs = $("#canvas-submit-members-table tr");
              var result = {};
              for (var i = 0; i < trs.length; i++) {
                var thistr = $(trs[i]);
                var netid = thistr.attr("data-netid");
                var studentid = courseStudents[netid];
                if (!studentid) {
                  continue;
                }
                var input = thistr.find("input");
                var grade = input.val();
                if (input.attr("type") == "checkbox") {
                  var complete = input.prop('checked');
                  if (complete) {
                    grade = "complete";
                  } else {
                    grade = "incomplete";
                  }
                }
                result[studentid] = grade;
              }
              var postCanvasGradesAjax = canvas_post_grade(assignmentID, result);
              postCanvasGradesAjax.done(function () {
                Materialize.toast('Successfully submitted attendance to Canvas!', 10000);
              });
              postCanvasGradesAjax.fail(function (data) {
                Materialize.toast('Error: ' + data.responseJSON.error, 10000);
              });
            }

            $("#admin-session-canvas-submit").click(openCanvasModal);
            function openCanvasModal() {
              $('#canvas-submit-modal').modal('open');
              var session = $("#admin-session-select option:selected").val();
              $("#canvas-submit-sessionname").html(session);
              var courseInfoAjax = canvas_show_course();
              courseInfoAjax.done(function (data) {
                $("#canvas-submit-coursename").html(data.name);
              });
              var listAssignmentsAjax = canvas_list_assignments();
              listAssignmentsAjax.done(function (data) {
                var template = $('#mustache_adminCanvasAssignmentOption').html();
                Mustache.parse(template);
                $("#canvas-submit-assignments-list > option:not([disabled])").remove();
                for (var i = 0; i < data.length; i++) {
                  var disdata = data[i];
                  var rendered = Mustache.render(template, disdata);
                  $("#canvas-submit-assignments-list").append(rendered);
                }
                $('select').material_select();
              });
              var listCourseStudentsAjax = canvas_list_users();
              listCourseStudentsAjax.done(function (data) {
                courseStudents = [];
                for (var i = 0; i < data.length; i++) {
                  var student = data[i];
                  courseStudents[student.login_id] = student.id;
                }
              });
            }
        }
      });
    </script>

    <script id="mustache_adminMembersTable" type="text/template">
      <tr>
        <td>{{id}}</td>
        <td>{{netid}}</td>
        <td>{{timestamp}}</td>
      </tr>
    </script>
    <script id="mustache_adminCanvasAssignmentOption" type="text/template">
      <option value="{{ id }}" data-grading-type="{{ grading_type }}">{{ name }}</option>
    </script>
    <script id="mustache_adminCanvasMembersTable" type="text/template">
      <tr data-netid="{{ netid }}">
          <td>{{ netid }}</td>
          {{#pass_fail}}
          <td>
              <div class="switch">
                  <label>
                      Incomplete
                      <input type="checkbox" checked=checked>
                      <span class="lever"></span> Complete
                  </label>
              </div>
          </td>
          {{/pass_fail}}
          {{^pass_fail}}
          <td>
              <div class="input-field inline">
                  <input placeholder="Grade" value="2">
              </div>
          </td>
          {{/pass_fail}}
      </tr>
    </script>
  </body>
</html>
