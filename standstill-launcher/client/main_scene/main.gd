extends PanelContainer

@onready var content_container: MarginContainer = $MarginContainer/HBoxContainer/ScrollContainer/PanelContainer/MarginContainer/PanelContainer/MarginContainer
@onready var register_scene: PackedScene = preload("res://client/register/register.tscn")


func set_panel(scene: PackedScene) -> void:
	var instantiated = scene.instantiate()
	for child in content_container.get_children():
		child.queue_free()
	content_container.add_child(instantiated)
	
	
func show_register() -> void:
	set_panel(register_scene)
	
	
func _ready() -> void:
	show_register()
