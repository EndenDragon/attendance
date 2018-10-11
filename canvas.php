<?php
	/*
		Interacts with the Canvas API to carry out automated tasks to the gradebook
	*/
	include_once("common.php");
	require_administrator();

	if (!isset($_GET["action"])) {
	    die_and_error("action parameter is missing");
	}

	$action = $_GET["action"];
	$api_url_base = "https://canvas.uw.edu/api/v1/courses/" . $canvas_course_id . "/";

	header("Content-type:application/json");
	if ($_SERVER['REQUEST_METHOD'] == "GET") {
	    if ($action == "show_course") {
	        show_course();
	    } else if ($action == "list_users") {
	        list_users();
	    } else if ($action == "list_sections") {
            list_sections();
        } else if ($action == "list_assignments") {
    		list_assignments();
	    } else {
	        die_and_error("invalid action provided");
	    }
	} else if ($_SERVER['REQUEST_METHOD'] == "POST") {
	    if ($action == "post_grade") {
	        post_grade();
	    } else {
	        die_and_error("invalid action provided");
	    }
	} else {
	    die_and_error("invalid request method: " . $_SERVER['REQUEST_METHOD']);
	}

	// Shows basic course details
	function show_course() {
	    $result = call_canvas_url("GET");
	    echo json_encode($result, JSON_PRETTY_PRINT);
	}

	// List all the users in the course
	function list_users() {
		$result = array();
		$data = array(
			"enrollment_type[]" => "student",
			"enrollment_state" => "active",
			"per_page" => "100",
			"page" => 1
		);
		while (count($result) % 100 == 0) {
			$returned = call_canvas_url("GET", "users", $data);
			$result = array_merge($result, $returned);
			$data["page"] += 1;
			if (count($returned) == 0) {
				break;
			}
		}
	    echo json_encode($result, JSON_PRETTY_PRINT);
	}

    // List all the sections in the course
    function list_sections() {
		$result = array();
		$data = array(
			"include[]" => "students",
			"per_page" => "10",
			"page" => 1
		);
		while (count($result) % 10 == 0) {
			$returned = call_canvas_url("GET", "sections", $data);
			$result = array_merge($result, $returned);
			$data["page"] += 1;
			if (count($returned) == 0) {
				break;
			}
		}
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

	// List all the assignments in the course
	function list_assignments() {
		$result = array();
		$data = array(
			"per_page" => "100",
			"page" => 1
		);
		while (count($result) % 100 == 0) {
			$returned = call_canvas_url("GET", "assignments", $data);
			$result = array_merge($result, $returned);
			$data["page"] += 1;
			if (count($returned) == 0) {
				break;
			}
		}
	    echo json_encode($result, JSON_PRETTY_PRINT);
	}

	// Post grade to the assignment in the course
	function post_grade() {
		if (!isset($_POST["assignment_id"])) {
		    die_and_error("assignment_id parameter is missing");
		}
		if (!isset($_POST["posted_grade"])) {
		    die_and_error("posted_grade parameter is missing");
		}

		$assignment_id = $_POST["assignment_id"];
	    $posted_grade = json_decode($_POST["posted_grade"]);
	    $input = array();
	    foreach ($posted_grade as $student_id => $grade) {
	    	$input["grade_data[" . $student_id . "][posted_grade]"] = $grade;
	    }
	    $result = call_canvas_url("POST", "assignments/" . $assignment_id . "/submissions/update_grades", $input);
	    echo json_encode($result, JSON_PRETTY_PRINT);
	}

	function call_canvas_url($method, $url_path = "", $data = array()) {
		global $canvas_access_token, $api_url_base;

		$url = $api_url_base . $url_path . "?access_token=" . $canvas_access_token;
		foreach ($data as $key => $value) {
			$url .= "&" . $key . "=" . $value;
		}

		$options = array(
			'http' => array('method' => "{$method}")
		);
		$context = stream_context_create($options);

		$json_response = file_get_contents($url, false, $context);

		if ($json_response === FALSE){
			$result = FALSE;
		} else {
			$result = json_decode($json_response);
		}
		return $result;
	}
?>
