class_name SSDMRegisterPanel
extends SSDMPanelBase

@onready var user_name_line_edit: LineEdit = $MarginContainer/VBoxContainer/GridContainer/UserNameLineEdit
@onready var display_name_line_edit: LineEdit = $MarginContainer/VBoxContainer/GridContainer/DisplayNameLineEdit
@onready var email_line_edit: LineEdit = $MarginContainer/VBoxContainer/GridContainer/EmailLineEdit
@onready var password_line_edit: LineEdit = $MarginContainer/VBoxContainer/GridContainer/PasswordLineEdit
@onready var re_password_line_edit: LineEdit = $MarginContainer/VBoxContainer/GridContainer/RePasswordLineEdit
@onready var error_label: Label = $MarginContainer/VBoxContainer/ErrorLabel
@onready var register_button: Button = $MarginContainer/VBoxContainer/CenterContainer/HBoxContainer/RegisterButton


func show_error(error: String) -> void:
	if error == "":
		error_label.hide()
		return
	error_label.text = error
	error_label.show()
	
	
func enable_panel(enable: bool) -> void:
	user_name_line_edit.editable = enable
	display_name_line_edit.editable = enable
	email_line_edit.editable = enable
	password_line_edit.editable = enable
	re_password_line_edit.editable = enable
	register_button.disabled = !enable
	
	
func _ready() -> void:
	error_label.hide()
	
	
func _on_login_button_pressed() -> void:
	pass


func _on_register_button_pressed() -> void:
	enable_panel(false)
	var register_request = SSDMRegisterRequest.new()
	register_request.set_client_id(OS.get_unique_id())
	register_request.set_request_type("register")
	register_request.set_user_name(user_name_line_edit.text)
	register_request.set_display_name(display_name_line_edit.text)
	register_request.set_email(email_line_edit.text)
	register_request.set_password(password_line_edit.text, re_password_line_edit.text)
	if register_request.error != "":
		show_error(register_request.error)
		enable_panel(true)
		return
	else:
		show_error("")
	show_error("Connecting to server.")
	
	var payload = register_request.serialize_request();
	var headers = ["Content-Type: application/json"]
	var http_request: HTTPRequest = HTTPRequest.new()
	add_child(http_request)
	http_request.request_completed.connect(_on_request_completed)
	http_request.request(SSDMNetworkGlobal.REGISTER_URL, headers, HTTPClient.METHOD_POST, payload)
	
	
func _on_request_completed(result, response_code, headers, body):
	enable_panel(true)
	if result == HTTPRequest.RESULT_SUCCESS and response_code == 200 and headers != null:
		if body.is_empty():
			print("EMPTY!")
		var response_object: Dictionary = JSON.parse_string(body.get_string_from_utf8())
		if response_object.has("success") and response_object.has("message"):
			if response_object.success:
				show_error("Your account has been registered successfully. Please check your email for account activation instructions.")
				return
			else:
				show_error(response_object.message)
				return
	show_error("No response from the server. Please check your internet connection and try again.")
			
		
func _on_cancel_button_pressed() -> void:
	show_home.emit()
