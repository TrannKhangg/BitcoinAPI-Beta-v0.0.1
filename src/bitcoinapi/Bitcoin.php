<?php
/*
Hi I am a PocketMine Plugin Developer :D. The plugin is still in beta. If you have any problems, please give me feedback: https://www.facebook.com/profile.php?id=100071316150096 
*/

namespace bitcoinapi;

use pocketmine\Server;
use pocketmine\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\event\Listener;

use pocketmine\utils\Config;

use pocketmine\event\player\PlayerJoinEvent;

class Bitcoin extends PluginBase implements Listener {
  
  public function onEnable(){
    $this->getLogger()->info("Bitcoin Beta-v0.0.1 đã được bật!");
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->bitcoin = new Config($this->getDataFolder() . "bitcoin.yml", Config::YAML);
  }
  
  public function onDisable(){
    $this->getLogger()->info("Bitcoin Beta-v0.0.1 đã tắt!");
  }
  
  public function onJoin(PlayerJoinEvent $event){
    $player = $event->getPlayer();
    if(!$this->bitcoin->exists($player->getName())){
      $this->bitcoin->set($player->getName(), 0);
      $this->bitcoin->save();
    }
  }
  
  public function reduceBitcoin($player, $bitcoin){
    if($player instanceof Player){
      if($bitcoin instanceof Int){
        return $this->bitcoin->set($player->getName(), ($this->bitcoin->get($player->getName()) - $bitcoin));
      }
    }
  }
  
  public function addBitcoin($player, $bitcoin){
    if($player instanceof Player){
      if($bitcoin instanceof Int){
        return $this->bitcoin->set($player->getName(), ($this->bitcoin->get($player->getName()) + $bitcoin));
      }
    }
  }
  
  public function myBitcoin($player){
    if($player instanceof Player){
      return ($this->bitcoin->get($player->getName()));
    }
  }
  
  public function getAllBitcoin(){
    return $this->bitcoin->getAll();
  }
  
  public function onCommand(CommandSender $sender, Command $cmd, String $label, array $args): bool{
    switch($cmd->getName()){
      case "mybitcoin":
        if($sender instanceof Player){
          $bitcoin = $this->myBitcoin($sender);
          $sender->sendMessage("§6§l•>§b Số Bitcoin của bạn:§c " . $bitcoin);
        }else{
          $sender->sendMessage("§c§lVui lòng sử dụng trong Game!");
        }
        break;
        
        case "setbitcoin":
          if($sender instanceof Player){
            if($sender->hasPermission("setbitcoin.pmmdst")){
              if(isset($args[0])){
                if(isset($args[1])){
                  $player = $this->getServer()->getPlayer($args[0]);
                  if(!is_numeric($args[1])){
                    $sender->sendMessage("§cYêu cầu : kí tự phải là 1 chữ số trở lên!");
                    return true;
                  }
                  if(!$player instanceof Player){
                    $sender->sendMessage("§c Người chơi " . $args[0] . " §bOffline!");
                    return true;
                  }
                  $this->bitcoin->set($player->getName(), $args[1]);
                  $this->bitcoin->save();
                  $sender->sendMessage("§a Thành công chỉnh số " . $args[0] . " Bitcoin thành " . $args[1]);
                  $player->sendMessage("§a Số Bitcoin của bạn đã được chỉnh thành " . $args[1]);
                }else{
                  $sender->sendMessage("Câu Lệnh: /setbitcoin {player} {amount}");
                }
              }else{
                $sender->sendMessage("Câu Lệnh: /setbitcoin {player} {amount}");
              }
            }
          }else{
            $sender->sendMessage("§c§lVui lòng sử dụng trong Game!");
          }
          break;
          
          case "topbitcoin":
            $bitcoinall = $this->getAllBitcoin();
            arsort($bitcoinall);
            $bitcoinall = array_slice($bitcoinall, 0, 9);
            $top = 1;
            foreach($bitcoinall as $name => $count){
              $sender->sendMessage("Top " . $top . " thuộc về " . $name . " với số lượng " . $count . " bitcoin");
              $top++;
            }
            break;
    }
    return true;
  }
}
