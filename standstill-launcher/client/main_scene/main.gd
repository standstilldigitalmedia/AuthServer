extends PanelContainer

@onready var content_container: MarginContainer = $MarginContainer/HBoxContainer/ScrollContainer/PanelContainer/MarginContainer/PanelContainer/MarginContainer
@onready var register_scene: PackedScene = preload("res://client/register/register.tscn")
@onready var activate_scene: PackedScene = preload("res://client/activate/activate.tscn")
@onready var login_scene: PackedScene = preload("res://client/login/login.tscn")

var register: SSDMRegisterPanel
var activate: SSDMActivatePanel
var login: SSDMLoginPanel


func set_panel(scene: SSDMPanelBase) -> void:
	for child in content_container.get_children():
		child.hide()
	scene.show()
	
	
func _on_account_registered(token):
	activate.register_token = token
	set_panel(activate)
	

func _on_show_home():
	set_panel(login)
	
	
func _on_show_register():
	set_panel(register)
	
	
func _on_account_activated():
	pass
	
	
func _ready() -> void:
	register = register_scene.instantiate()
	activate = activate_scene.instantiate()
	login = login_scene.instantiate()
	register.account_registered.connect(_on_account_registered)
	register.show_home.connect(_on_show_home)
	activate.show_home.connect(_on_show_home)
	activate.account_activated.connect(_on_account_activated)
	login.show_register.connect(_on_show_register)
	content_container.add_child(register)
	content_container.add_child(activate)
	content_container.add_child(login)
	set_panel(activate)
