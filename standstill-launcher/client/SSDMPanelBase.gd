class_name SSDMPanelBase
extends PanelContainer

signal show_home()
signal show_register()
signal show_login()
signal account_registered(token: String)
signal account_activated()

@onready var error_label: Label


func show_error(error: String) -> void:
	if error == "":
		error_label.hide()
		return
	error_label.text = error
	error_label.show()
