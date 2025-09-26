extends PanelContainer

@onready var content_container: MarginContainer = $MarginContainer/HBoxContainer/ScrollContainer/PanelContainer/MarginContainer/PanelContainer/MarginContainer
@onready var login_scene: PackedScene = preload("res://client/login/login.tscn")
@onready var register_scene: PackedScene = preload("res://client/register_scene/register.tscn")

var login_panel: SSDMHomePanel
var register_panel: SSDMRegisterPanel

func set_panel(scene: SSDMPanelBase) -> void:
	for child in content_container.get_children():
		child.hide()
	scene.show()
	
	
func show_home() -> void:
	set_panel(login_panel)
	
	
func show_register() -> void:
	set_panel(register_panel)
	
	
func _ready() -> void:
	login_panel = login_scene.instantiate()
	register_panel = register_scene.instantiate()
	login_panel.show_home.connect(show_home)
	login_panel.show_register.connect(show_register)
	register_panel.show_home.connect(show_home)
	content_container.add_child(login_panel)
	content_container.add_child(register_panel)
	show_register()
