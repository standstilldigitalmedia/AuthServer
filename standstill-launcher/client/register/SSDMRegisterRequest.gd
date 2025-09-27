class_name SSDMRegisterRequest
extends SSDMRequestBase

var user_name := ""
var display_name := ""
var email := ""
var password := ""


func set_user_name(value: String) -> void:
	if value.is_empty():
		set_error("Username must be provided.")
		return
	if value.length() < SSDMNetworkGlobal.MIN_NAME_LENGTH:
		set_error("Username must be at least " + str(SSDMNetworkGlobal.MIN_NAME_LENGTH) + " characters long.")
		return
	if value.length() > SSDMNetworkGlobal.MAX_NAME_LENGTH:
		set_error("Username must not be more than " + str(SSDMNetworkGlobal.MAX_NAME_LENGTH) + " characters long.")
		return
	if contains_special_characters(value):
		set_error("Username can not contain special characters.")
		return
	user_name = value
	
	
func set_display_name(value: String) -> void:
	if value.is_empty():
		set_error("Display name must be provided.")
		return
	if value.length() < SSDMNetworkGlobal.MIN_NAME_LENGTH:
		set_error("Display name must be at least " + str(SSDMNetworkGlobal.MIN_NAME_LENGTH) + " characters long.")
		return
	if value.length() > SSDMNetworkGlobal.MAX_NAME_LENGTH:
		set_error("Display name must not be more than " + str(SSDMNetworkGlobal.MAX_NAME_LENGTH) + " characters long.")
		return
	if contains_special_characters(value):
		set_error("Display name can not contain special characters.")
	display_name = value


func set_email(value: String) -> void:
	if value.is_empty():
		set_error("Email address must be provided.")
		return
	if value.length() < SSDMNetworkGlobal.MIN_EMAIL_LENGTH:
		set_error("Email address must be at least " + str(SSDMNetworkGlobal.MIN_EMAIL_LENGTH) + " characters long.")
		return
	if value.length() > SSDMNetworkGlobal.MAX_EMAIL_LENGTH:
		set_error("Email address must not be more than " + str(SSDMNetworkGlobal.MAX_EMAIL_LENGTH) + " characters long.")
		return
	if !value.contains("@"):
		set_error("Invalid email address.")
		return
	if !value.contains("."):
		set_error("Invalid email address.")
		return
	email = value
	
	
func set_password(value: String, repassword: String) -> void:
	if value.is_empty():
		set_error("Password must be provided.")
		return
	if value != repassword:
		set_error("Passwords must match.")
		return
	if value.length() < SSDMNetworkGlobal.MIN_PASSWORD_LENGTH:
		set_error("Password must be at least " + str(SSDMNetworkGlobal.MIN_PASSWORD_LENGTH) + " characters long.")
		return
	if value.length() > SSDMNetworkGlobal.MAX_PASSWORD_LENGTH:
		set_error("Password must not be more than " + str(SSDMNetworkGlobal.MAX_PASSWORD_LENGTH) + " characters long.")
		return
	if SSDMNetworkGlobal.PASSWORD_STRENGTH > 0:
		if !contains_lowercase(value):
			set_error("Password must contain at least one lowercase character.")
			return
		if !contains_uppercase(value):
			set_error("Password must contain at least one uppercase character.")
			return
		if SSDMNetworkGlobal.PASSWORD_STRENGTH > 1:
			if !contains_number(value):
				set_error("Password must contain at least one number.")
				return
			if SSDMNetworkGlobal.PASSWORD_STRENGTH > 2:
				if !contains_special_characters(value):
					set_error("Password must contain at least one special character.")
					return
	password = value


func serialize_request() -> String:
	var return_dictionary = serialize_base()
	return_dictionary["user_name"] = user_name
	return_dictionary["display_name"] = display_name
	return_dictionary["email"] = email
	return_dictionary["password"] = password
	return JSON.stringify(return_dictionary)
	

func deserialize_request(input_string: String) -> void:
	deserialize_base(input_string)
	var input_object = JSON.parse_string(input_string)
	if !input_object.has("user_name"):
		set_error("Username must be provided.")
		return
	if !input_object.has("display_name"):
		set_error("Display name must be provided.")
		return
	if !input_object.has("email"):
		set_error("Email address must be provided")
		return
	if !input_object.has("password"):
		set_error("Password must be provided")
		return
	set_user_name(input_object.user_name)
	set_display_name(input_object.display_name)
	set_email(input_object.email)
	set_password(input_object.password, input_object.password)
