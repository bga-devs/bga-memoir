<?php

namespace M44\States;

use M44\Core\Notifications;
use M44\Core\Globals;
use M44\Helpers\Utils;
use M44\Managers\Cards;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Models\Player;

trait PlayCardTrait
{
  function stPlayCard()
  {
    if (Globals::getCommissar() != '' && $this->isInitialCommissar()) {
      $pId = Teams::get(Globals::getCommissar())->getCommander();
      $this->changeActivePlayerAndJumpTo($pId, \ST_COMMISSAR);
    }
  }

  function argsPlayCard($player = null)
  {
    $singleActive = is_null($player);
    $player = $player ?? Players::getActive();
    $cards = $player
      ->getCards()
      ->filter(function ($card) {
        return $card->getType() != \CARD_AMBUSH;
      })
      ->map(function ($card) {
        // get section restriction (if applicable)
        return $card->getAdditionalPlayConstraints();
      });

    $cardsHill317 = [];
    if ($player->canHill317()) {
      $cardsHill317 = $player
        ->getCards()
        ->filter(function ($card) {
          return $card->canHill317();
        })
        ->getIds();
    }

    $cardBlowBridge = [];
    if ($player->canBlowBridge()) {
      $cardBlowBridge = $player
        ->getCards()
        ->filter(function ($card) {
          return $card->canBlowBridge();
        })
        ->getIds();
    }

    $cardArmorBreakthrough = [];
    if ($player->canArmorBreakthrough()) {
      $cardArmorBreakthrough = $player
        ->getCards()
        ->filter(function ($card) {
          return $card->canArmorBreakthrough();
        })
        ->getIds();
    }


    $hasAirPowerTokens = $player->getTeam()->hasAirPowerTokens();

    $args = [
      'cards' => $cards,
      'cardsHill317' => $cardsHill317,
      'cardsBlowBridge' => $cardBlowBridge,
      'cardsArmorBreakthrough' => $cardArmorBreakthrough,
      'actionCount' => Globals::getActionCount(),
    ];
    $args = $singleActive ? Utils::privatise($args) : $args;
    $args['hasAirpowerToken'] = $hasAirPowerTokens;
    return $args;
  }

  function actPlayCard($cardId, $sectionId = null, $hill317 = false, $canBlowbridge = false, $airPowerTokenUsed = false, $armorBreakthrough = false)
  {
    // Sanity check
    $this->checkAction('actPlayCard');
    Globals::incActionCount();
    $player = Players::getCurrent();
    $args = $this->argsPlayCard($player);
   
    if ($airPowerTokenUsed) {
      // create a flag AirPowerToken Used to future conditions as no card used
      Globals::setAirPowerTokenUsed(true);
      // remove AirPower Token
      $airPowerTokens = Globals::getAirPowerTokens();
      $teamId = $player->getTeam()->getId();
      unset($airPowerTokens[array_search($teamId, $airPowerTokens)]);
      Globals::setAirPowerTokens($airPowerTokens);
      // notify
      Notifications::removeAirpowerToken($player);
      $card = Cards::getInstance(CARD_AIR_POWER);
      $card->setPlayer($player->getId());
      $cardId = Cards::getIdByType(CARD_AIR_POWER);
      $card->setId($cardId);      
      
      $nextState = $card->nextStateAfterPlay();
    } else {

      if (!in_array($cardId, $args['cards']->getIds())) {
        throw new \BgaVisibleSystemException('Non playable card. Should not happen.');
      }
  
      if (!is_null($args['cards'][$cardId]) && (!in_array($sectionId, $args['cards'][$cardId]) || is_null($sectionId))) {
        throw new \BgaVisibleSystemException('Invalid section. Should not happen');
      }
  
      if (is_null($args['cards'][$cardId]) && !is_null($sectionId)) {
        throw new \BgaVisibleSystemException('Invalid section. Should not happen');
      }
  
      if ($hill317 && !$player->canHill317()) {
        throw new \BgaVisibleSystemException('Cannot play card as hill317. Should not happen');
      }
  
      if ($hill317 && !Cards::get($cardId)->canHill317()) {
        throw new \BgaVisibleSystemException('Cannot play this type of card as hill317. Should not happen');
      }

      if ($armorBreakthrough && !$player->canArmorBreakthrough()) {
        throw new \BgaVisibleSystemException('Cannot play this type of card as Armor Breakthrough. Should not happen');
      }
  
      if ($hill317) {
        $card = Cards::get($cardId);
        $card->setExtraDatas('hill317', true);
      }
  
      if ($canBlowbridge) {
        $card = Cards::get($cardId);
        $card->setExtraDatas('canblowbridge', true);
      }

      if ($armorBreakthrough) {
        $card = Cards::get($cardId);
        $card->setExtraDatas('canArmorBreakthrough', true);
      }
  
      // Play the card
      $card = Cards::play($player, $cardId, $sectionId);
      $card->onPlay();
      Notifications::playCard($player, $card);
      $nextState = $card->nextStateAfterPlay();
  
      // Handle first turn of Russian
      if ($player->isCommissar() && $player->getCommissarCard() == null) {
        $nextState = 'commissar';
      }
    }

    $this->nextState($nextState);
  }

  function argsDistributeCards($player = NULL) 
  { // Overlord distribution cards args
    $singleActive = is_null($player);
    $player = $player ?? Players::getActive();
    $teamId = $player ->getTeam()-> getId();
    $cards = $player
      ->getCards()
      ->filter(function ($card) {
        return $card->getType() != \CARD_AMBUSH;
      })
      ->map(function ($card) use ($teamId) {
        // get subsection restriction (if applicable)
        return [$card->getPlayableSubSections($teamId), $card->isOverlord2subsections()];
      });

    $cardsHill317 = [];
    if ($player->canHill317()) {
      $cardsHill317 = $player
        ->getCards()
        ->filter(function ($card) {
          return $card->canHill317();
        })
        ->getIds();
    }

    $cardBlowBridge = [];
    if ($player->canBlowBridge()) {
      $cardBlowBridge = $player
        ->getCards()
        ->filter(function ($card) {
          return $card->canBlowBridge();
        })
        ->getIds();
    }

    $cardArmorBreakthrough = [];
    if ($player->canArmorBreakthrough()) {
      $cardArmorBreakthrough = $player
        ->getCards()
        ->filter(function ($card) {
          return $card->canArmorBreakthrough();
        })
        ->getIds();
    }

    // give list of already distributed cards to avoid double selection 
    $distributedCards = Globals::getDistributedCards();
    $teamId = $player ->getTeam()-> getId();
    if (isset($distributedCards[$teamId])) {
      foreach ($distributedCards[$teamId] as $distributedCard) {
        $cardIdDistributed = $distributedCard['cardId'];
        unset($cards[$cardIdDistributed]);
      }
    }

    // give list of selected subSections to avoid double selection
    $selectedSubSections = [];
    if (isset($distributedCards[$teamId])) {
      foreach ($distributedCards[$teamId] as $distributedCard) {
        // manage case of card with 2 sections selected
        $distributedCardInstance = Cards::get($distributedCard['cardId']);
        if ($distributedCardInstance->isOverlord2subsections()) {
          $sectionId = floor($distributedCard['subSection']/2);    
          $selectedSubSections[] = $sectionId*2;
          $selectedSubSections[] = $sectionId*2 + 1;
        } else {
        $selectedSubSections[] = intval($distributedCard['subSection']);
        }
      }
    }

    //only in Campaign Mode (not probable to have Campaign mode in overlord so far)
    //$hasAirPowerTokens = $player->getTeam()->hasAirPowerTokens();
    

    $args = [
      'cards' => $cards,
      'cardsHill317' => $cardsHill317,
      'cardsBlowBridge' => $cardBlowBridge,
      'cardsArmorBreakthrough' => $cardArmorBreakthrough,
      'actionCount' => Globals::getActionCount(),
      'selectedSubSections' => $selectedSubSections,
    ];
    $args = $singleActive ? Utils::privatise($args) : $args;
    return $args;
  }

  function stDistributeCards()
  { // Overlord distribution cards state
    // Commander preparation Choose 1 to 3 cards to be played on mains sections and get list of those cards
    
    // TO DO specific Comissar in Est Theater extensions
    /*if (Globals::getCommissar() != '' && $this->isInitialCommissar()) {
      $pId = Teams::get(Globals::getCommissar())->getCommander();
      $this->changeActivePlayerAndJumpTo($pId, \ST_COMMISSAR);
    }*/
    $commander = Players::getActive()->getTeam()->getCommander();
    $this->argsDistributeCards($commander);
    

  }

  function actDistributeCards($cardId, $sectionId, $finished = false)
  { // 1 to 3 cards choosen from UI to be played on mains sections and get list of those cards
    // prepare list of those cards and place them on corresponding main and secondary sections
    // to be added on Globals card List to be played later

    // Store the cardId and its selected subSection in Globals database
    if (!$finished) { // if finished, no card is selected but we just want to end distribution phase
      $list = Globals::getDistributedCards();
      $commander = Players::getActive()->getTeam()->getCommander();
      $teamId = $commander ->getTeam()-> getId();
      $list[$teamId][] = ['cardId' => $cardId, 'subSection' => $sectionId, 'played' => false];
      Globals::setDistributedCards($list);
      // push card in overlord distributed cards list of player to be able to display them in UI
      $card = Cards::distribute($commander, $cardId, $sectionId);
      $card->setExtraDatas('subsection', $sectionId);
      // update UI in subSection
      Notifications::distributeCard($commander, $card, $sectionId); 
    }
    
    // Check if others cards can be still distributed
    // Condition max number, condition 1 car maxi per sub section and max 1 commander chief
    if ($finished || count($list[$teamId]) >= 3) {
      // max 3 cards distributed
      $this->nextState('overlordPlayCard'); // Next state after distribution to be confirmed
    } else {
      $args = $this->argsDistributeCards($commander);
      $this->nextState('distributeCards'); // stay in distribution cards state
    }
  }

  function stOverlordPlayCard()
  {
    $commander = Players::getActive()->getTeam()->getCommander();
    $this->argsOverlordPlayCard();
  }

  function argsOverlordPlayCard($player = null)
  {
    $singleActive = is_null($player);  
    // Get list of cards distributed and not yet played for the active commander during distribution phase
    $list = Globals::getDistributedCards();
    $commander = Players::getActive()->getTeam()->getCommander();
    $teamId = $commander ->getTeam()-> getId();
    $cardsToBePlayedTmp = $list[$teamId];
    $cardsToBePlayed = array_filter($cardsToBePlayedTmp, function ($card) {
      return $card['played'] === false;  // definir les status des cartes à distribuer (ex: inOrder, played, etc.) pour éviter de devoir faire des mises à jour dans la liste des cartes distribuées et pouvoir gérer plus facilement les différentes étapes de distribution et de jeu des cartes distribuées 
    });

    $cardsHill317 = [];
    if ($commander->canHill317()) {
      $cardsHill317 = $commander
        ->getCards()
        ->filter(function ($card) {
          return $card->canHill317();
        })
        ->getIds();
    }

    $cardBlowBridge = [];
    if ($commander->canBlowBridge()) {
      $cardBlowBridge = $commander
        ->getCards()
        ->filter(function ($card) {
          return $card->canBlowBridge();
        })
        ->getIds();
    }

    $cardArmorBreakthrough = [];
    if ($commander->canArmorBreakthrough()) {
      $cardArmorBreakthrough = $commander
        ->getCards()
        ->filter(function ($card) {
          return $card->canArmorBreakthrough();
        })
        ->getIds();
    }

    
    // get first Chief Commander card is distributed (section 6)
    foreach ($cardsToBePlayed as $card) {
      if ($card['subSection'] == 6) {
      $cards = $commander
      ->getCards()
      ->filter(function ($card) {
        return $card->getId() == $card['cardId'];
      })
      ->map(function ($card) {
        // get section restriction (if applicable)
        return $card->getAdditionalPlayConstraints();
      });

        $args = [
          'cards' => $cards,
          'cardsHill317' => $cardsHill317,
          'cardsBlowBridge' => $cardBlowBridge,
          'cardsArmorBreakthrough' => $cardArmorBreakthrough,
          'actionCount' => Globals::getActionCount(), 
        ];
        $args = $singleActive ? Utils::privatise($args) : $args;
        return $args;
      }
    };


    // otherwise deal other cards distributed to be selected fo selected units
    $cardsToBePlayedIds = array_map(function ($card) {
      return $card['cardId'];
    }, $cardsToBePlayed);

    $cards = $commander
      ->getCardsOverlordDistributed()
      ->filter(function ($card) use ($cardsToBePlayedIds) {
        return in_array($card->getId(), $cardsToBePlayedIds);
      })
      ->map(function ($card) {
        // get section restriction (if applicable)
        return $card->getAdditionalPlayConstraints();
      });

    $args = [
          'cards' => $cards,
          'cardsHill317' => $cardsHill317,
          'cardsBlowBridge' => $cardBlowBridge,
          'cardsArmorBreakthrough' => $cardArmorBreakthrough,
          'actionCount' => Globals::getActionCount(), 
      ];
    $args = $singleActive ? Utils::privatise($args) : $args;
    return $args;
  }

  function actOverlordPlayCard($cardId, $sectionId = null, $hill317 = false, $canBlowbridge = false, $airPowerTokenUsed = false, $armorBreakthrough = false)
  {
    // Sanity check
    $this->checkAction('actPlayCard');
    Globals::incActionCount();
    $player = Players::getCurrent();
    if (Globals::isOverlord()) {
      $args = $this->argsOverlordPlayCard($player);
    }
    // When card played, update distributed card list in Globals with played = true for this card to avoid being proposed again if several cards distributed
    //var_dump('actOverlordPlayCard', $cardId);
    // get card from distributed card list in Globals
    $list = Globals::getDistributedCards();
    $commander = Players::getActive()->getTeam()->getCommander();
    $teamId = $commander ->getTeam()-> getId();
    $listCard = $list[$teamId];
    foreach ($listCard as $key => $card) {
      //var_dump($key, $card);
      if ($card['cardId'] == $cardId) {
        $list[$teamId][$key]['played'] = true;
        Globals::setDistributedCards($list);
        break;
      }
    }
    // then go to Overlord Order Units state with this card and its section restriction as arguments to be able to select units to order based on those restrictions
    if (!in_array($cardId, $args['cards']->getIds())) {
      throw new \BgaVisibleSystemException('Non playable card. Should not happen.');
    }
  
    if (!is_null($args['cards'][$cardId]) && (!in_array($sectionId, $args['cards'][$cardId]) || is_null($sectionId))) {
      throw new \BgaVisibleSystemException('Invalid section. Should not happen');
    }
  
    if (is_null($args['cards'][$cardId]) && !is_null($sectionId)) {
      throw new \BgaVisibleSystemException('Invalid section. Should not happen');
    }

    if ($hill317 && !$player->canHill317()) {
      throw new \BgaVisibleSystemException('Cannot play card as hill317. Should not happen');
    }

    if ($hill317 && !Cards::get($cardId)->canHill317()) {
      throw new \BgaVisibleSystemException('Cannot play this type of card as hill317. Should not happen');
    }

    if ($armorBreakthrough && !$player->canArmorBreakthrough()) {
      throw new \BgaVisibleSystemException('Cannot play this type of card as Armor Breakthrough. Should not happen');
    }

    if ($hill317) {
      $card = Cards::get($cardId);
      $card->setExtraDatas('hill317', true);
    }

    if ($canBlowbridge) {
      $card = Cards::get($cardId);
      $card->setExtraDatas('canblowbridge', true);
    }

    if ($armorBreakthrough) {
      $card = Cards::get($cardId);
      $card->setExtraDatas('canArmorBreakthrough', true);
    }

    // Play the card
    $card = Cards::play($player, $cardId, $sectionId);
    $card->onPlay();
    Notifications::playCard($player, $card);
    $nextState = $card->nextStateAfterPlay();

    // Handle first turn of Russian
    if ($player->isCommissar() && $player->getCommissarCard() == null) {
      $nextState = 'commissar';
    }

     $this->nextState($nextState);
  }
}
