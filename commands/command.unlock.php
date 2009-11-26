<?
class COMMAND_UNLOCK
{
	var $NAME = "COMMAND_UNLOCK";
     var $ENABLED;
     var $REQUIRED_FLAG = "";
          /*   ENABELED = bool
               Denotes the class is active and available
          */
    
     function COMMAND_UNLOCK(&$sys_message)
     {
          $this->MESSAGE =& $sys_message;
          $this->MESSAGE->SYSMESSAGE($this->NAME, "SYSTEM", "LOAD COMMAND");
          $this->ENABLED = True;
          return True;
     }
     function INITIALIZE($OBJECT_ARRAY)
     {
          foreach($OBJECT_ARRAY as $key => &$value)
          {
               if(!$this->{$key} =& $value){return False;}
          }
          $this->MESSAGE->SYSMESSAGE($this->NAME, "SYSTEM", "INIT COMMAND");
          return True;
     }
     function LOAD_COMMANDS()
     {
          $this->INTERPRETER->ADD_ACTION($this->NAME,"UNLOCK", "UNLOCK", "", $this->REQUIRED_FLAG);
          $this->PFLAGS->ADD_FLAG($this->REQUIRED_FLAG);
          return True;
     }
     function UNLOCK($target)
     {
		$socket = $this->SYSTEM->CURRENT_SOCKET;
          $target = strtoupper($target);
          $cmd = explode(" ",$target);
		if($target != "")
		{
			if(trim($cmd[0]) == "DOOR")
			{
				$direction = $this->MOVEMENT->GET_DIRECTION_CODE($cmd[1]);
				if($direction == "0")
				{
			          $this->COMMUNICATION->SEND_TO_CHAR("Please specify the direction the door is in.\n");
				}else{
				     $zone = $this->PLAYER->GET_USER_ZONE($this->PLAYER->GET_UID_BY_SOCKET($socket));
				     $room = $this->PLAYER->GET_USER_ROOM($this->PLAYER->GET_UID_BY_SOCKET($socket));
					if($this->EXITS->EXIT_EXISTS($zone, $room, $direction))
					{
						if($this->EXITS->EXIT_HAS_DOOR($zone, $room, $direction))
						{
					          if($this->EXITS->IS_DOOR_CLOSED($zone, $room, $direction))
					          {
								if($this->EXITS->IS_DOOR_LOCKABLE($zone, $room, $direction))
								{
						          	if($this->EXITS->GET_LOCK_OWNER($zone, $room, $direction) != $this->PLAYER->GET_UID_BY_SOCKET($socket) && $this->EXITS->GET_LOCK_OWNER($zone, $room, $direction) != null)
						          	{
										$this->COMMUNICATION->SEND_TO_CHAR("You are not the person who locked the door, only the person who locked the door can unlock it.\n");
						          	}else{
							          	if($this->EXITS->IS_DOOR_UNLOCKED($zone, $room, $direction))
							          	{
								          	$this->COMMUNICATION->SEND_TO_CHAR("That door is already unlocked.\n");
							          	}else{
								          	$this->EXITS->UNLOCK_DOOR($zone, $room, $direction);
								          	$this->EXITS->UNLOCK_DOOR($this->EXITS->GET_EXIT_DEST_ZONE($zone, $room, $direction), $this->EXITS->GET_EXIT_DEST_ROOM($zone, $room, $direction), $this->MOVEMENT->GET_REVERSE_DIRECTION($direction));
								          	$this->EXITS->CLEAR_LOCK_OWNER($zone, $room, $direction);
								          	$this->EXITS->CLEAR_LOCK_OWNER($this->EXITS->GET_EXIT_DEST_ZONE($zone, $room, $direction), $this->EXITS->GET_EXIT_DEST_ROOM($zone, $room, $direction), $this->MOVEMENT->GET_REVERSE_DIRECTION($direction));
								          	$this->COMMUNICATION->SEND_TO_CHAR("The door is now unlocked.\n");
							          	}
						          	}
						          }else{
						          	$this->COMMUNICATION->SEND_TO_CHAR("That door does not have a lock on it.\n");
						     	}
					          }else{
						          $this->COMMUNICATION->SEND_TO_CHAR("You can not unlock an opened door.\n");
					          }
						}else{
					          $this->COMMUNICATION->SEND_TO_CHAR("There is no door in that direction.\n");
						}
					}else{
				          $this->COMMUNICATION->SEND_TO_CHAR("There is no exit in that direction.\n");
					}
				}
			}else{
	          	$this->COMMUNICATION->SEND_TO_CHAR("You do not have a '$cmd[0]'\n");
			}
		}else{
          	$this->COMMUNICATION->SEND_TO_CHAR("What do you want to unlock?\n");
		}
     }  
}
?>