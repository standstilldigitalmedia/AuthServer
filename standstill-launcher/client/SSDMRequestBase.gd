class_name SSDMRequestBase
extends RefCounted

var error := ""
var client_id := ""


func set_error(value: String) -> void:
	if error == "":
		error = value
		
				
func set_client_id(value: String):
	if value.is_empty():
		set_error("Invalid request: No client ID")
		return
	if value.length() > SSDMNetworkGlobal.MAX_CLIENT_ID_LENGTH:
		set_error("Invalid request: Client ID is too long.")
		return
	client_id = value
	
	
func contains_uppercase(input_string: String) -> bool:
	for char_code in input_string.to_ascii_buffer():
		# Convert ASCII code to a character string for comparison
		var single_char = char(char_code)
		if single_char != single_char.to_lower():
			return true
	return false
	
	
func contains_lowercase(input_string: String) -> bool:
	for char_code in input_string.to_ascii_buffer():
		# Convert ASCII code to a character string for comparison
		var single_char = char(char_code)
		if single_char != single_char.to_upper():
			return true
	return false
	
	
func contains_number(input_string):
	for char_code in input_string.to_ascii_buffer():
		# Convert ASCII code to a character string for comparison
		var single_char = char(char_code)
		if single_char.is_valid_int():
			return true
	return false
	
	
func contains_special_characters(text: String) -> bool:
	var regex = RegEx.new()
	regex.compile("[^a-zA-Z0-9_]") 
	return regex.search(text) != null


func serialize_base() -> Dictionary:
	var return_dictionary: Dictionary = {}
	return_dictionary["client_id"] = client_id
	return return_dictionary
	
	
func deserialize_base(input_string: String) -> void:
	var input_object = JSON.parse_string(input_string)
	if !input_object.has("client_id"):
		set_error("Client ID must be provided.")
		return
	set_client_id(input_object.client_id)
