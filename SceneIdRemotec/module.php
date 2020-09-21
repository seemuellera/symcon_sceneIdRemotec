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
			"1" => "Single click",
			"2" => "Double click",
			"3" => "Tripple click"
		);
		
		$this->SceneIdents = Array(
			"1" => "CentralScene1",
			"2" => "CentralScene2",
			"3" => "CentralScene3",
			"4" => "CentralScene4",
			"5" => "CentralScene5",
			"6" => "CentralScene6",
			"7" => "CentralScene7",
			"8" => "CentralScene8",
			"15" => "CentralScene15"
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
		
		/**
		
		// Exit the function if the scene ID is disabled
		switch ($sceneId) {
		
			case "16":
				if(! $this->ReadPropertyBoolean("SceneS1SingleClickEnabled") ) {
					return;
				}
				$this->DeviceHandler($this->ReadPropertyInteger("SceneS1SingleClickTarget"), $this->ReadPropertyString("SceneS1SingleClickAction"), $this->ReadPropertyInteger("SceneS1SingleClickDimValue"));
				break;
			case "14":
				if(! $this->ReadPropertyBoolean("SceneS1DoubleClickEnabled") ) {
					return;
				}
				$this->DeviceHandler($this->ReadPropertyInteger("SceneS1DoubleClickTarget"), $this->ReadPropertyString("SceneS1DoubleClickAction"), $this->ReadPropertyInteger("SceneS1DoubleClickDimValue"));
				break;
			case "12":
				if(! $this->ReadPropertyBoolean("SceneS1HoldEnabled") ) {
					return;
				}
				$this->DeviceHandler($this->ReadPropertyInteger("SceneS1HoldTarget"), $this->ReadPropertyString("SceneS1HoldAction"), $this->ReadPropertyInteger("SceneS1HoldDimValue"));
				break;
			case "13":
				if(! $this->ReadPropertyBoolean("SceneS1HoldEnabled") ) {
					return;
				}
				$this->DeviceHandler($this->ReadPropertyInteger("SceneS1ReleaseTarget"), $this->ReadPropertyString("SceneS1ReleaseAction"), $this->ReadPropertyInteger("SceneS1ReleaseDimValue"));
				break;
			case "26":
				if(! $this->ReadPropertyBoolean("SceneS2SingleClickEnabled") ) {
					return;
				}
				$this->DeviceHandler($this->ReadPropertyInteger("SceneS2SingleClickTarget"), $this->ReadPropertyString("SceneS2SingleClickAction"), $this->ReadPropertyInteger("SceneS2SingleClickDimValue"));
				break;
			case "24":
				if(! $this->ReadPropertyBoolean("SceneS2DoubleClickEnabled") ) {
					return;
				}
				$this->DeviceHandler($this->ReadPropertyInteger("SceneS2DoubleClickTarget"), $this->ReadPropertyString("SceneS2DoubleClickAction"), $this->ReadPropertyInteger("SceneS2DoubleClickDimValue"));
				break;
			case "25":
				if(! $this->ReadPropertyBoolean("SceneS2TrippleClickEnabled") ) {
					return;
				}
				$this->DeviceHandler($this->ReadPropertyInteger("SceneS2TrippleClickTarget"), $this->ReadPropertyString("SceneS2TrippleClickAction"), $this->ReadPropertyInteger("SceneS2TrippleClickDimValue"));
				break;
			case "22":
				if(! $this->ReadPropertyBoolean("SceneS2HoldEnabled") ) {
					return;
				}
				$this->DeviceHandler($this->ReadPropertyInteger("SceneS2HoldTarget"), $this->ReadPropertyString("SceneS2HoldAction"), $this->ReadPropertyInteger("SceneS2HoldDimValue"));
				break;
			case "23":
				if(! $this->ReadPropertyBoolean("SceneS2HoldEnabled") ) {
					return;
				}
				$this->DeviceHandler($this->ReadPropertyInteger("SceneS2ReleaseTarget"), $this->ReadPropertyString("SceneS2ReleaseAction"), $this->ReadPropertyInteger("SceneS2ReleaseDimValue"));
				break;
			default:
				throw new Exception("Invalid Scene ID" . $sceneId);
		}
		
		**/
		
		SetValue($this->GetIDForIdent("LastTrigger"), time());
		SetValue($this->GetIDForIdent("LastAction"), $this->SceneNames[$sceneId]);
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
