<?php

// Klassendefinition
class SceneIdRemotec extends IPSModule {
 
	// Der Konstruktor des Moduls
	// Überschreibt den Standard Kontruktor von IPS
	public function __construct($InstanceID) {
		// Diese Zeile nicht löschen
		parent::__construct($InstanceID);

		// Selbsterstellter Code
		$this->SceneNames = Array(
			"0" => "Single click",
			"1" => "Release",
			"2" => "Hold",
			"3" => "Double click"
		);
		
		$this->SceneIdents = Array(
			"1" => "CentralScene1",
			"2" => "CentralScene2",
			"3" => "CentralScene3",
			"4" => "CentralScene4",
			"5" => "CentralScene5",
			"6" => "CentralScene6",
			"7" => "CentralScene7",
			"8" => "CentralScene8"
		);
		
		$this->SceneActions = Array(
			Array(
				"caption" => "Toggle Status",
				"value" => "Toggle"
			),
			Array(
				"caption" => "Switch On",
				"value" => "SwitchOn"
			),
			Array(
				"caption" => "Switch Off",
				"value" => "SwitchOff"
			),
			Array(
				"caption" => "Dim to a specifc value",
				"value" => "DimToValue"
			),
			Array(
				"caption" => "Change to a specifc Color",
				"value" => "ChangeToColor"
			)
		);
	}

	// Überschreibt die interne IPS_Create($id) Funktion
	public function Create() {
		
		// Diese Zeile nicht löschen.
		parent::Create();

		// Properties - Global
		$this->RegisterPropertyString("Sender","SceneIdRemotec");
		$this->RegisterPropertyInteger("RefreshInterval",0);
		$this->RegisterPropertyInteger("TargetInstance",0);
		$this->RegisterPropertyBoolean("DebugOutput",false);
		$this->RegisterPropertyString("SceneConfiguration","");
		
		// Variables
		$this->RegisterVariableInteger("LastTrigger","Last Trigger","~UnixTimestamp");
		$this->RegisterVariableString("LastAction","Last Action");

		// Default Actions
		// $this->EnableAction("Status");

		// Timer
		$this->RegisterTimer("RefreshInformation", 0 , 'SCENEIDREMOTEC_RefreshInformation($_IPS[\'TARGET\']);');
    }

	public function Destroy() {

		// Never delete this line
		parent::Destroy();
	}
 
	// Überschreibt die intere IPS_ApplyChanges($id) Funktion
	public function ApplyChanges() {

		$newInterval = $this->ReadPropertyInteger("RefreshInterval") * 1000;
		$this->SetTimerInterval("RefreshInformation", $newInterval);
		
		$allTargetVariableIds = $this->GetTargetVariableIds();
		
		foreach($allTargetVariableIds as $currentVariable) {
			
			$this->RegisterMessage($currentVariable, VM_UPDATE);
		}
		
		// Diese Zeile nicht löschen
		parent::ApplyChanges();
	}


	public function GetConfigurationForm() {
        	
		// Initialize the form
		$form = Array(
            		"elements" => Array(),
					"actions" => Array()
        		);

		// Add the Elements
		$form['elements'][] = Array("type" => "NumberSpinner", "name" => "RefreshInterval", "caption" => "Refresh Interval");
		$form['elements'][] = Array("type" => "CheckBox", "name" => "DebugOutput", "caption" => "Enable Debug Output");
		$form['elements'][] = Array("type" => "SelectInstance", "name" => "TargetInstance", "caption" => "Remotec Instance");
		
		// Add the buttons for the test center
		$form['actions'][] = Array(	"type" => "Button", "label" => "Refresh", "onClick" => 'SCENEIDREMOTEC_RefreshInformation($id);');
		
		$form['elements'][] = Array(
								"type" => "List", 
								"name" => "SceneConfiguration", 
								"caption" => "Scene configuration",
								"rowCount" => 8,
								"add" => false,
								"delete" => false,
								"columns" => Array(
									Array(
										"caption" => "Button",
										"name" => "Button",
										"width" => "150px",
										"edit" => Array("type" => "ValidationTextBox")
									),
									Array(
										"caption" => "Single Click Enabled",
										"name" => "SingleClickEnabled",
										"width" => "100px",
										"edit" => Array("type" => "CheckBox"),
										"add" => false
									),
									Array(
										"caption" => "Single Click Action",
										"name" => "SingleClickAction",
										"width" => "250px",
										"edit" => Array(
													"type" => "Select",
													"options" => $this->SceneActions
												),
										"add" => "Toggle"
									),
									Array(
										"caption" => "Single Click Action Variable",
										"name" => "SingleClickActionVariable",
										"width" => "auto",
										"edit" => Array("type" => "SelectVariable"),
										"add" => 0,
										"visible" => false
									),
									Array(
										"caption" => "Single Click Action Parameter",
										"name" => "SingleClickActionParameter",
										"width" => "auto",
										"edit" => Array("type" => "NumberSpinner"),
										"add" => 0,
										"visible" => false
									),
									Array(
										"caption" => "Double Click Enabled",
										"name" => "DoubleClickEnabled",
										"width" => "100px",
										"edit" => Array("type" => "CheckBox"),
										"add" => false
									),
									Array(
										"caption" => "Double Click Action",
										"name" => "DoubleClickAction",
										"width" => "250px",
										"edit" => Array(
													"type" => "Select",
													"options" => $this->SceneActions
												),
										"add" => "Toggle"
									),
									Array(
										"caption" => "Double Click Action Variable",
										"name" => "DoubleClickActionVariable",
										"width" => "auto",
										"edit" => Array("type" => "SelectVariable"),
										"add" => 0,
										"visible" => false
									),
									Array(
										"caption" => "Double Click Action Parameter",
										"name" => "DoubleClickActionParameter",
										"width" => "auto",
										"edit" => Array("type" => "NumberSpinner"),
										"add" => 0,
										"visible" => false
									),
									Array(
										"caption" => "Hold Enabled",
										"name" => "HoldEnabled",
										"width" => "100px",
										"edit" => Array("type" => "CheckBox"),
										"add" => false
									),
									Array(
										"caption" => "Hold Action",
										"name" => "HoldAction",
										"width" => "250px",
										"edit" => Array(
													"type" => "Select",
													"options" => $this->SceneActions
												),
										"add" => "Toggle"
									),
									Array(
										"caption" => "Hold Action Variable",
										"name" => "HoldActionVariable",
										"width" => "auto",
										"edit" => Array("type" => "SelectVariable"),
										"add" => 0,
										"visible" => false
									),
									Array(
										"caption" => "Hold Action Parameter",
										"name" => "HoldActionParameter",
										"width" => "auto",
										"edit" => Array("type" => "NumberSpinner"),
										"add" => 0,
										"visible" => false
									),
									Array(
										"caption" => "Release Enabled",
										"name" => "ReleaseEnabled",
										"width" => "100px",
										"edit" => Array("type" => "CheckBox"),
										"add" => false
									),
									Array(
										"caption" => "Release Action",
										"name" => "ReleaseAction",
										"width" => "250px",
										"edit" => Array(
													"type" => "Select",
													"options" => $this->SceneActions
												),
										"add" => "Toggle"
									),
									Array(
										"caption" => "Release Action Variable",
										"name" => "ReleaseActionVariable",
										"width" => "auto",
										"edit" => Array("type" => "SelectVariable"),
										"add" => 0,
										"visible" => false
									),
									Array(
										"caption" => "Release Action Parameter",
										"name" => "ReleaseActionParameter",
										"width" => "auto",
										"edit" => Array("type" => "NumberSpinner"),
										"add" => 0,
										"visible" => false
									)
								),
								"values" => Array(
									Array(
										"Button" => "1",
										"SingleClickEnabled" => false,
										"SingleClickAction" => "Toggle",
										"SingleClickActionVariable" => 0,
										"SingleClickActionParameter" => 0,
										"DoubleClickEnabled" => false,
										"DoubleClickAction" => "Toggle",
										"DoubleClickActionVariable" => 0,
										"DoubleClickActionParameter" => 0,
										"HoldEnabled" => false,
										"HoldAction" => "Toggle",
										"HoldActionVariable" => 0,
										"HoldActionParameter" => 0,
										"ReleaseEnabled" => false,
										"ReleaseAction" => "Toggle",
										"ReleaseActionVariable" => 0,
										"ReleaseActionParameter" => 0
									),
									Array(
										"Button" => "2",
										"SingleClickEnabled" => false,
										"SingleClickAction" => "Toggle",
										"SingleClickActionVariable" => 0,
										"SingleClickActionParameter" => 0,
										"DoubleClickEnabled" => false,
										"DoubleClickAction" => "Toggle",
										"DoubleClickActionVariable" => 0,
										"DoubleClickActionParameter" => 0,
										"HoldEnabled" => false,
										"HoldAction" => "Toggle",
										"HoldActionVariable" => 0,
										"HoldActionParameter" => 0,
										"ReleaseEnabled" => false,
										"ReleaseAction" => "Toggle",
										"ReleaseActionVariable" => 0,
										"ReleaseActionParameter" => 0
									),
									Array(
										"Button" => "3",
										"SingleClickEnabled" => false,
										"SingleClickAction" => "Toggle",
										"SingleClickActionVariable" => 0,
										"SingleClickActionParameter" => 0,
										"DoubleClickEnabled" => false,
										"DoubleClickAction" => "Toggle",
										"DoubleClickActionVariable" => 0,
										"DoubleClickActionParameter" => 0,
										"HoldEnabled" => false,
										"HoldAction" => "Toggle",
										"HoldActionVariable" => 0,
										"HoldActionParameter" => 0,
										"ReleaseEnabled" => false,
										"ReleaseAction" => "Toggle",
										"ReleaseActionVariable" => 0,
										"ReleaseActionParameter" => 0
									),
									Array(
										"Button" => "4",
										"SingleClickEnabled" => false,
										"SingleClickAction" => "Toggle",
										"SingleClickActionVariable" => 0,
										"SingleClickActionParameter" => 0,
										"DoubleClickEnabled" => false,
										"DoubleClickAction" => "Toggle",
										"DoubleClickActionVariable" => 0,
										"DoubleClickActionParameter" => 0,
										"HoldEnabled" => false,
										"HoldAction" => "Toggle",
										"HoldActionVariable" => 0,
										"HoldActionParameter" => 0,
										"ReleaseEnabled" => false,
										"ReleaseAction" => "Toggle",
										"ReleaseActionVariable" => 0,
										"ReleaseActionParameter" => 0
									),
									Array(
										"Button" => "5",
										"SingleClickEnabled" => false,
										"SingleClickAction" => "Toggle",
										"SingleClickActionVariable" => 0,
										"SingleClickActionParameter" => 0,
										"DoubleClickEnabled" => false,
										"DoubleClickAction" => "Toggle",
										"DoubleClickActionVariable" => 0,
										"DoubleClickActionParameter" => 0,
										"HoldEnabled" => false,
										"HoldAction" => "Toggle",
										"HoldActionVariable" => 0,
										"HoldActionParameter" => 0,
										"ReleaseEnabled" => false,
										"ReleaseAction" => "Toggle",
										"ReleaseActionVariable" => 0,
										"ReleaseActionParameter" => 0
									),
									Array(
										"Button" => "6",
										"SingleClickEnabled" => false,
										"SingleClickAction" => "Toggle",
										"SingleClickActionVariable" => 0,
										"SingleClickActionParameter" => 0,
										"DoubleClickEnabled" => false,
										"DoubleClickAction" => "Toggle",
										"DoubleClickActionVariable" => 0,
										"DoubleClickActionParameter" => 0,
										"HoldEnabled" => false,
										"HoldAction" => "Toggle",
										"HoldActionVariable" => 0,
										"HoldActionParameter" => 0,
										"ReleaseEnabled" => false,
										"ReleaseAction" => "Toggle",
										"ReleaseActionVariable" => 0,
										"ReleaseActionParameter" => 0
									),
									Array(
										"Button" => "7",
										"SingleClickEnabled" => false,
										"SingleClickAction" => "Toggle",
										"SingleClickActionVariable" => 0,
										"SingleClickActionParameter" => 0,
										"DoubleClickEnabled" => false,
										"DoubleClickAction" => "Toggle",
										"DoubleClickActionVariable" => 0,
										"DoubleClickActionParameter" => 0,
										"HoldEnabled" => false,
										"HoldAction" => "Toggle",
										"HoldActionVariable" => 0,
										"HoldActionParameter" => 0,
										"ReleaseEnabled" => false,
										"ReleaseAction" => "Toggle",
										"ReleaseActionVariable" => 0,
										"ReleaseActionParameter" => 0
									),
									Array(
										"Button" => "8",
										"SingleClickEnabled" => false,
										"SingleClickAction" => "Toggle",
										"SingleClickActionVariable" => 0,
										"SingleClickActionParameter" => 0,
										"DoubleClickEnabled" => false,
										"DoubleClickAction" => "Toggle",
										"DoubleClickActionVariable" => 0,
										"DoubleClickActionParameter" => 0,
										"HoldEnabled" => false,
										"HoldAction" => "Toggle",
										"HoldActionVariable" => 0,
										"HoldActionParameter" => 0,
										"ReleaseEnabled" => false,
										"ReleaseAction" => "Toggle",
										"ReleaseActionVariable" => 0,
										"ReleaseActionParameter" => 0
									)
								)
							);

		// Return the completed form
		return json_encode($form);

	}
	
	protected function GetTargetVariableIds() {
		
		$variableIds = Array();
		
		foreach ($this->SceneIdents as $button => $ident) {
			
			$variableIds[$button] = IPS_GetObjectIDByIdent($ident, $this->ReadPropertyInteger("TargetInstance"));
		}
		
		return $variableIds;
	}
	
	protected function GetSceneNumber(int $variableId) {
		
		$variableIds = $this->GetTargetVariableIds();
		
		$sceneNumber = array_search($variableId, $variableIds);
		
		return $sceneNumber;
	}
	
	protected function LogMessage($message, $severity = 'INFO') {
		
		if ( ($severity == 'DEBUG') && ($this->ReadPropertyBoolean('DebugOutput') == false )) {
			
			return;
		}
		
		$messageComplete = $severity . " - " . $message;
		
		IPS_LogMessage($this->ReadPropertyString('Sender') . " - " . $this->InstanceID, $messageComplete);
	}

	public function RefreshInformation() {

		$this->LogMessage("Refresh in Progress", "DEBUG");
		
		$this->LogMessage(print_r($this->GetTargetVariableIds()), "DEBUG");
	}

	public function RequestAction($Ident, $Value) {
	
	
		switch ($Ident) {
		
			case "Status":
				SetValue($this->GetIDForIdent($Ident), $Value);
				break;
			default:
				throw new Exception("Invalid Ident");
		}
	}
	
	public function MessageSink($TimeStamp, $SenderId, $Message, $Data) {
	
		$this->LogMessage("$TimeStamp - $SenderId - $Message - " . implode(",",$Data) , "DEBUG");
		
		if ($Data[3] == $Data[4]) {
			
			$this->LogMessage("Duplicate Event. Ignoring it", "DEBUG");
			return;
		}
		
		$sceneId = $Data[0];
		$sceneNumber = $this->GetSceneNumber($SenderId);
		
		$this->LogMessage("Scene configuration " . print_r($this->GetSceneConfiguration($sceneNumber)), "DEBUG");
		
		SetValue($this->GetIDForIdent("LastTrigger"), time());
		SetValue($this->GetIDForIdent("LastAction"), "Button " . $sceneNumber . ": " . $this->SceneNames[$sceneId]);
	}
	
	protected function GetSceneConfiguration($sceneNumber) {
		
		$sceneConfigurationJson = $this->ReadPropertyString("SceneConfiguration");
		$sceneConfiguration = json_decode($sceneConfigurationJson, true);
		
		if (! is_array($sceneConfiguration)) {
			
			$this->LogMessage("Scene Configuration is faulty","ERROR");
			return false;
		}
		
		if (count($sceneConfiguration) != 8) {
			
			$this->LogMessage("Scene Configuration is faulty. Incorrect Number of scenes.","ERROR");
			return false;
		}
		
		return $sceneConfiguration[$sceneNumber];
	}

	protected function DeviceHandler($targetId, $action, $specificValue = false) {
		
		switch ($action) {
			
			case "Toggle":
				$this->ToggleDevice($targetId);
				return;
			case "SwitchOn":
				$this->SwitchDeviceOn($targetId);
				return;
			case "SwitchOff":
				$this->SwitchDeviceOff($targetId);
				return;
			case "DimToValue":
				$this->DimDeviceToValue($targetId, $specificValue);
				return;
			case "ChangeToColor":
				$this->ChangeDeviceToColor($targetId, $specificValue);
				return;
			default:
				throw new Exception("Action not yet implemented");
		}
	}
	
	protected function ToggleDevice($targetId) {
		
		if (GetValue($targetId) ) {
			
			RequestAction($targetId, false);
		}
		else {
			
			RequestAction($targetId, true);
		}
	}
	
	protected function SwitchDeviceOn($targetId) {
			
		RequestAction($targetId, true);
	}
	
	protected function SwitchDeviceOff($targetId) {
			
		RequestAction($targetId, false);
	}
	
	protected function DimDeviceToValue($targetId, $value) {
			
		RequestAction($targetId, $value);
	}
	
	protected function ChangeDeviceToColor($targetId, $value) {
			
		RequestAction($targetId, $value);
	}
}
