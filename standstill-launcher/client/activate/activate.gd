class_name SSDMActivatePanel
extends SSDMPanelBase

@onready var activation_code_line_edit := $MarginContainer/VBoxContainer/GridContainer/ActivationCodeLineEdit
@onready var activate_button := $MarginContainer/VBoxContainer/CenterContainer/HBoxContainer/ActivateButton
@onready var cancel_button := $MarginContainer/VBoxContainer/CenterContainer/HBoxContainer/CancelButton
var register_token: String


func _on_ready():
	error_label = $MarginContainer/VBoxContainer/ErrorLabel
	show_error("")


func enable_panel(enable: bool) -> void:
	activation_code_line_edit.editable = enable
	activate_button.disabled = !enable
	cancel_button.disabled = !enable
	

func _on_cancel_button_pressed() -> void:
	show_home.emit()


func _on_activate_button_pressed() -> void:
	enable_panel(false)
	var request_activation = SSDMRequestActivation.new()
	request_activation.set_token(register_token)
	request_activation.set_code(activation_code_line_edit.text)
	request_activation.set_client_id(OS.get_unique_id())
	if request_activation.error != "":
		show_error(request_activation.error)
		enable_panel(true)
		return
	else:
		show_error("")
	show_error("Connecting to server.")
	
	var payload = request_activation.serialize_request();
	var headers = ["Content-Type: application/json"]
	var http_request: HTTPRequest = HTTPRequest.new()
	add_child(http_request)
	http_request.request_completed.connect(_on_request_completed)
	http_request.request(SSDMNetworkGlobal.ACTIVATE_URL, headers, HTTPClient.METHOD_POST, payload)
	
	
func _on_request_completed(result, response_code, headers, body):
	enable_panel(true)
	if result == HTTPRequest.RESULT_SUCCESS and response_code == 200 and headers != null:
		if body.is_empty():
			print("EMPTY!")
		var response_object: Dictionary = JSON.parse_string(body.get_string_from_utf8())
		if response_object.has("success") and response_object.has("message"):
			if response_object.success:
				if response_object.has("token"):
					account_activated.emit()
				else:
					show_error("There was a problem with the server. Please try again.")
				return
			else:
				show_error(response_object.message)
				return
	show_error("No response from the server. Please check your internet connection and try again.")
