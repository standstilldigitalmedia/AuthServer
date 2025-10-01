class_name SSDMRequestActivation
extends SSDMRequestBase

var token: String
var code: String


func set_token(value: String):
	token = value
	
	
func set_code(value: String):
	code = value
	
	
func serialize_request() -> String:
	var return_dictionary = serialize_base()
	return_dictionary["token"] = token
	return_dictionary["code"] = code
	return JSON.stringify(return_dictionary)
	

func deserialize_request(input_string: String) -> void:
	deserialize_base(input_string)
	var input_object = JSON.parse_string(input_string)
	if !input_object.has("token"):
		set_error("Invalid request.")
		return
	if !input_object.has("code"):
		set_error("Invalid request.")
		return
	set_token(input_object.token)
	set_code(input_object.code)
