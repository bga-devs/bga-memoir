define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
  const MEDAL_ELIMINATION = 1;
  const MEDAL_POSITION = 2;
  const MEDAL_EXIT = 6;

  return declare('memoir.players', null, {
    getPlayerColor(pId) {
      return this.gamedatas.players[pId].color;
    },

    setupPlayers() {
      this._handCounters = {};
      this._reserveTokenCounter = {};
      if (!this.isSpectator) {
        dojo.place('<div id="m44-player-hand"></div>', 'm44-bottom-container');
        this.gamedatas.players[this._pId].cards.forEach((card) => this.addCard(card, 'm44-player-hand'));
      }

      this.forEachPlayer((player) => {
        let pos = player.team == this._bottomTeam ? 'bottom' : 'top';
        this.place('tplPlayerPanel', player, pos + '-team-players');
        dojo.place(
          `
          <div class='player-panel-wrapper'>
            <div class="m44-team-name" data-team="${player.team}">${_(player.team)}</div>
            <div id="player-panel-holder-${player.id}" class="player-panel-holder"></div>
          </div>`,
          'overall_player_board_' + player.id,
        );
        dojo.place(`<div class='card-in-play' id='in-play-${player.id}'></div>`, pos + '-in-play');
        this._handCounters[player.id] = this.createCounter(`hand-count-${player.id}`, player.cardsCount);
        if(player.isCampaign) {
          this._reserveTokenCounter[player.id] = this.createCounter(`reservetokens-count-${player.id}`, player.nreservetoken);
        }

        if (player.inplay) {
          this.addCard(player.inplay, 'in-play-' + player.id);
        }

        if (player.commissarCard) {
          let container = 'commissar-' + player.id;
          if (player.commissarCard === true) {
            this.addCardBack(container);
          } else {
            this.addCard(player.commissarCard, container);
          }
        }

        if (player.isCommissar && player.id == this.player_id) {
          dojo.place('commissar-holder-' + player.id, 'm44-player-hand', 'first');
        }
      });
    },

    updateLayout(layout) {
      $('m44-container').dataset.layout = layout;
      if (layout == 1) {
        dojo.place('m44-top-part', 'm44-top-container', 'first');
        dojo.place('m44-bottom-part', 'm44-bottom-container', 'first');
        dojo.place('top-medals', 'top-team', 'first');
        dojo.place('bottom-medals', 'bottom-team', 'first');
        dojo.place('top-team-name', 'top-team', 'first');
        dojo.place('bottom-team-name', 'bottom-team', 'first');
        dojo.place('top-staging-area', 'm44-top-hrule');
        dojo.place('bottom-staging-area', 'm44-bottom-hrule');

      } else if (layout == 0) {
        dojo.place('m44-top-part', 'left-holder', 'first');
        dojo.place('m44-bottom-part', 'left-holder', 'last');
        dojo.place('top-team-name', 'm44-top-hrule');
        dojo.place('bottom-team-name', 'm44-bottom-hrule');
        dojo.place('top-medals', 'm44-top-hrule');
        dojo.place('bottom-medals', 'm44-bottom-hrule');
        dojo.place('top-staging-area', 'm44-top-hrule');
        dojo.place('bottom-staging-area', 'm44-bottom-hrule');
      }

      this.forEachPlayer((player) => {
        let container = (layout == 1 ? 'm44-player-pannel-' : 'player-panel-holder-') + player.id;
        dojo.place(`hand-count-holder-${player.id}`, container);
        if ($(`commissar-holder-${player.id}`)) {
          dojo.place(`commissar-holder-${player.id}`, container);
        }
        if(player.isCampaign){
          dojo.place(`reservetokens-holder-${player.id}`, container);
        }

        if (player.isCommissar && player.id == this.player_id) {
          dojo.place(`commissar-holder-${player.id}`, 'm44-player-hand', 'first');
        }
      });
    },

    updatePlayers() {
      this.forEachPlayer((player) => {
        this._handCounters[player.id].setValue(player.cardsCount);
        this._reserveTokenCounter[player.id].setValue(player.nreservetoken);
      });
    },

    updateHand() {
      let player = this.gamedatas.players[this.player_id];
      if (player.inplay) {
        this.addCard(player.inplay, 'in-play-' + player.id);
      }
      if (player.commissarCard) {
        this.addCard(player.commissarCard, 'commissar-' + player.id);
      }
      player.cards.forEach((card) => this.addCard(card, 'm44-player-hand'));
    },

    tplPlayerPanel(player) {
      let img = $('avatar_' + player.id);
      let commissar = player.isCommissar
        ? `
        <div class='commissar-holder' id="commissar-holder-${player.id}">
          <div class='commissar-token'></div>
          <div class='commissar-slot' id='commissar-${player.id}'></div>
        </div>`
        : '';
      
      const NATION_SPRITES = ['GB', 'DE', 'US', 'FR'];
      let sprite = player.isCampaign
      ?
      NATION_SPRITES.findIndex((t) => t == player.campaignNation)
      : 0;
      console.log(player.id, player.campaignNation, sprite);
      let restokens = player.isCampaign
      ? `
        <div class='reservetokens-holder' id="reservetokens-holder-${player.id}">
          <div class='reserve-token' data-sprite="${sprite}"></div>
          <div class='reservetoken-count' id="reservetokens-count-${player.id}">${player.nreservetoken}</div>
        </div>`
        : '';
      // <div class='reserve-token' data-sprite="${sprite}"></div>
      return `<div class='m44-player-panel' id="m44-player-pannel-${player.id}">
        <div class='player-avatar'>
          <img src="${img.src}" alt="" class="avatar emblem" />
        </div>
        <div class='player-name' style='color:#${player.color}' data-commissar="${player.isCommissar ? 1 : 0}">${
        player.name
      }</div>
        <div class='hand-count-holder' id='hand-count-holder-${player.id}'>
          <div class='hand-count-back'></div>
          <div class='hand-count' id="hand-count-${player.id}">${player.cardsCount}</div>
        </div>
        ${commissar}
        ${restokens}
      </div>`;
    },

    setupTeams() {
      this.gamedatas.teams.forEach((team) => {
        let pos = this._bottomTeam == team.team ? 'bottom' : 'top';
        // Add basic infos
        $(`${pos}-team-name`).dataset.team = team.team;
        $(`${pos}-team-name`).innerHTML = _(team.team);

        // Add medals
        for (let i = 0; i < team.victory; i++) {
          dojo.place('<div class="m44-medal-slot"></div>', pos + '-medals-slots');
        }

        Object.values(team.medals).forEach((medal) => this.addMedal(medal));

        // Add reserve units slots on staging area
        for (let i = 0; i < 2; i++) {
            dojo.place('<div class="reserve-unit"></div>', pos + '-staging-slots');
        }
      });
    },

    updateTeams() {
      this.gamedatas.teams.forEach((team) => {
        Object.values(team.medals).forEach((medal) => this.addMedal(medal));
      });
    },

    updateTeamStatus(teamId, status = 'idle') {
      let pos = this._bottomTeam == teamId ? 'bottom' : 'top';
      let pos2 = pos == 'top' ? 'bottom' : 'top';
      $(pos + '-team-status').dataset.status = status;
      $(pos2 + '-team-status').dataset.status = 'idle';
    },

    ////////////////////////////////////
    //  __  __          _       _
    // |  \/  | ___  __| | __ _| |___
    // | |\/| |/ _ \/ _` |/ _` | / __|
    // | |  | |  __/ (_| | (_| | \__ \
    // |_|  |_|\___|\__,_|\__,_|_|___/
    ////////////////////////////////////
    addMedal(medal, container = null) {
      if (container == null) {
        let pos = this._bottomTeam == medal.team ? 'bottom' : 'top';
        container = pos + '-medals-container';
      }

      if (medal.type == MEDAL_POSITION) {
        let boardMedal = $(`board-medal-${medal.foreign_id}`);
        if (boardMedal.dataset.permanent == 1) {
          boardMedal.classList.add('hide');
        }
      }
      this.place('tplMedal', medal, container);
    },

    tplMedal(medal) {
      const SPRITES = ['medal1', 'medal2', 'medal4', 'medal5', 'medal6', 'medal7', 'medal8', 'medal9'];

      let sprite = SPRITES.findIndex((t) => t == medal.sprite);
      let content = '';
      if (medal.type == MEDAL_ELIMINATION || medal.type == MEDAL_EXIT) {
        content = `<div class="m44-unit" data-type="${medal.unit_type}" data-nation="${medal.unit_nation}" data-badge="${medal.unit_badge}" data-orientation="0">
                  <div class="m44-unit-meeple"></div>
          </div>`;
      }
      return `<div id='medal-${medal.id}' class='m44-medal' data-type='${medal.type}' data-sprite='${sprite}'>
          ${content}
        </div>`;
    },

    notif_scoreMedals(n) {
      debug('Notif: a team gained a medal', n);
      n.args.medals.forEach((medal) => {
        let container = null;
        if (n.args.cell) {
          container = `cell-${n.args.cell.x}-${n.args.cell.y}`;
        } else if (medal.type == MEDAL_POSITION) {
          container = `board-medal-${medal.foreign_id}`;
        } else {
          console.error('No container for medal, should not happen');
        }
        this.addMedal(medal, container);

        let pos = this._bottomTeam == medal.team ? 'bottom' : 'top';
        this.slide('medal-' + medal.id, pos + '-medals-container');
      });
    },

    notif_removeMedals(n) {
      debug('Notif: a team losed a medal', n);
      n.args.medalIds.forEach((medalId) => {
        this.slide('medal-' + medalId, 'board-medal-' + n.args.boardMedalId, {
          destroy: true,
        });
      });
    },

    notif_removeSectionMedals(n) {
      debug('Notif: a team losed a medal for section', n);
      n.args.medalIds.forEach((medalId) => {
        this.slide('medal-' + medalId, 'cell-container-14-4', {
          destroy: true,
        });
      });
    },

    /////////////////////////////////
    //   ____              _
    //  / ___|__ _ _ __ __| |___
    // | |   / _` | '__/ _` / __|
    // | |__| (_| | | | (_| \__ \
    //  \____\__,_|_|  \__,_|___/
    /////////////////////////////////

    addCard(card, container) {
      this.place('tplMemoirCard', card, container);
      this.addCustomTooltip('card-' + card.id, this.tplMemoirCard(card, true));
    },

    addCardBack(container) {
      let card = {
        id: this._backCardIdCounter--,
        name: '',
        text: [''],
        subtitle: '',
        location: '',
        value: 0,
        type: -1,
      };

      return this.place('tplMemoirCard', card, container);
    },

    tplMemoirCard(card, tooltip = false) {
      let isSection = card.type < 20;
      this.formatDesc(card);
      card.asset = card.type + parseInt(card.value);
      let className = isSection ? 'section-card' : 'tactic-card';
      if (card.location.substr(0, 6) == 'inplay') {
        className += ' inplay';
      }

      return (
        `<div id='card-${card.id + (tooltip ? '-tooltip' : '')}' class='m44-card ${className}' data-type='${
          card.asset
        }'>
          <div class='card-resizable'>
            <div class='card-title'>${_(card.name)}</div> ` +
        (isSection ? `<div class='card-subtitle'>${_(card.subtitle)}</div>` : '') +
        `
            <div class='card-text-container'>
              <div class='card-text'>${card.desc}</div>
            </div>
          </div>
        </div>`
      );
    },

    formatDesc(card) {
      if (card.desc) return;

      card.desc = '<div>' + card.text.map((t) => _(t)).join('</div><div>') + '</div>';
      card.desc = card.desc.replace(new RegExp('<ARMOR>', 'g'), '<span class="desc-unit">' + _('ARMOR') + '</span>');
      card.desc = card.desc.replace(
        new RegExp('<INFANTRY>', 'g'),
        '<span class="desc-unit">' + _('INFANTRY') + '</span>',
      );
      card.desc = card.desc.replace(
        new RegExp('<ARTILLERY>', 'g'),
        '<span class="desc-unit">' + _('ARTILLERY') + '</span>',
      );
    },

    onEnteringStatePlayCard(args) {
      let cards = args._private.cards;
      let cards317 = args._private.cardsHill317;
      let cardsBlowBridge = args._private.cardsBlowBridge;
      Object.keys(cards).forEach((cardId) => {
        this.onClick(`card-${cardId}`, () => {
          if (cards[cardId]) {
            this.clientState('playCardSelectSection', _('Choose target section'), { cardId, sections: cards[cardId] });
          } else if (cards317.includes(parseInt(cardId))) {
            this.clientState('playCardHill317', _('Do you wish to play it as Air Power card?'), { cardId });
          } else if (cardsBlowBridge.includes(parseInt(cardId))) {
            this.clientState('playCardBlowBridge', _('Do you wish to play it to try to blow a bridge ?'), { cardId });
          }
          else {
            this.takeAction('actPlayCard', { cardId });
          }
        });
      });
    },

    onEnteringStatePlayCardSelectSection(args) {
      let cardId = args.cardId;
      $(`card-${cardId}`).classList.add('choice');
      this.addCancelStateBtn();
      let sections = {
        0: _('Left'),
        1: _('Central'),
        2: _('Right'),
      };
      args.sections.forEach((section) => {
        this.addPrimaryActionButton(`btnSection-${section}`, sections[section], () =>
          this.takeAction('actPlayCard', { cardId, section }),
        );
      });
    },

    onEnteringStatePlayCardHill317(args) {
      let cardId = args.cardId;
      this.addCancelStateBtn();
      this.addPrimaryActionButton(`btnHillYes`, _('Yes'), () =>
        this.takeAction('actPlayCard', { cardId, hill317: true }),
      );

      this.addPrimaryActionButton(`btnHillNo`, _('No'), () =>
        this.takeAction('actPlayCard', { cardId, hill317: false }),
      );
    },

    onEnteringStatePlayCardBlowBridge(args) {
      let cardId = args.cardId;
      this.addCancelStateBtn();
      this.addPrimaryActionButton(`btnBlowBridgeYes`, _('Yes'), () =>
        this.takeAction('actPlayCard', { cardId, blowbridge: true }),
      );

      this.addPrimaryActionButton(`btnBlowBridgeNo`, _('No'), () =>
        this.takeAction('actPlayCard', { cardId, blowbridge: false }),
      );
    },



    notif_playCard(n) {
      debug('Notif: playing a card', n);
      if (this.player_id == n.args.player_id) {
        this.slide('card-' + n.args.card.id, `in-play-${n.args.player_id}`);
      } else {
        this.addCard(n.args.card, `in-play-${n.args.player_id}`);
      }
      this._handCounters[n.args.player_id].incValue(-1);
    },

    notif_discardCard(n) {
      debug('Notif: discarding a card', n);
      if (!$('card-' + n.args.card.id)) {
        this.addCard(n.args.card, `in-play-${n.args.player_id}`);
      }
      this.slide('card-' + n.args.card.id, 'discard', { duration: 1100 });
      this._handCounters[n.args.player_id].incValue(n.args.handCounter);
    },

    notif_discardCardItalianHighCommand(n) {
      debug('Notif: discarding a card', n);
      this.notif_discardCard(n);
      this._handCounters[n.args.player_id].incValue(-1);
    },

    notif_reshuffle(n) {
      debug('Notif: reshuffling the deck', n);
      $('discard').childNodes.forEach((card) => {
        this.slide(card, 'deck', {
          destroy: true,
        });
      });
      this._deckCounter.toValue(n.args.nDeck);
    },

    notif_drawCards(n) {
      debug('Notif: a player is drawing card(s)', n);
      if (this.player_id == n.args.player_id) {
        return 1;
      }

      for (let i = 0; i < n.args.nb; i++) {
        let card = this.addCardBack('deck');
        this.slide(card, `in-play-${n.args.player_id}`, {
          destroy: true,
        });
      }
      this._handCounters[n.args.player_id].incValue(n.args.nb);
      this._deckCounter.incValue(-n.args.nb);
    },

    notif_pDrawCards(n) {
      debug('Notif: you are drawing card(s)', n);
      n.args.cards.forEach((card) => {
        this.addCard(card, 'deck');
        this.slide('card-' + card.id, 'm44-player-hand');
      });
      this._deckCounter.incValue(-n.args.cards.length);
      this._handCounters[this.player_id].incValue(n.args.cards.length);
    },

    // Recon
    onEnteringStateDrawChoice(args) {
      // TODO : handle the case for nKeep > 1
      Object.values(args._private.cards).forEach((card) => {
        if (!$('card-' + card.id)) {
          this.addCard(card, 'm44-player-hand');
        }

        $('card-' + card.id).classList.add('choice');
        this.onClick(`card-${card.id}`, () =>
          this.clientState('drawChoiceChoice', _('Keep it or discard it?'), { cardId: card.id }),
        );
      });
    },

    onEnteringStateDrawChoiceChoice(args) {
      this.addCancelStateBtn();
      $('card-' + args.cardId).classList.add('choice');
      this.addPrimaryActionButton('btnKeep', _('Keep'), () =>
        this.takeAction('actChooseCard', { cardId: args.cardId, choice: 0 }),
      );
      this.addDangerActionButton('btnDiscard', _('Discard'), () =>
        this.takeAction('actChooseCard', { cardId: args.cardId, choice: 1 }),
      );
    },

    onEnteringStateCommissarCard(args) {
      let cards = args._private.cards;
      Object.keys(cards).forEach((cardId) => {
        this.onClick(`card-${cardId}`, () => {
          if (args._private.playableCards[cardId] !== undefined) {
            this.clientState('commissarCardChoice', _('Select how to use this card'), {
              cardId,
              hill317: args._private.playableCards[cardId],
            });
          } else {
            this.takeAction('actCommissarCard', { cardId });
          }
        });
      });
    },

    onEnteringStateCommissarCardChoice(args) {
      let cardId = args.cardId;
      this.addCancelStateBtn();
      this.addPrimaryActionButton(`btnCommissar`, _('Put it under commissar token'), () =>
        this.takeAction('actCommissarCard', { cardId }),
      );
      this.addPrimaryActionButton(`btnCommissarPlay`, _('Play it'), () =>
        this.takeAction('actPlayCard', { cardId, hill317: false }),
      );

      if (args.hill317) {
        this.addPrimaryActionButton(`btnCommissarPlay317`, _('Play it as an air power'), () =>
          this.takeAction('actPlayCard', { cardId, hill317: true }),
        );
      }
    },

    notif_commissarCard(n) {
      debug('Notif: a player is putting his card under his commissar token', n);
      if (this.player_id == n.args.player_id) {
        return 1;
      }
      let card = this.addCardBack(`hand-count-${n.args.player_id}`);
      this.slide(card, `commissar-${n.args.player_id}`);
      this._handCounters[n.args.player_id].incValue(-1);
    },

    notif_pCommissarCard(n) {
      debug('Notif: you are putting a card under your commissar token', n);
      this.slide('card-' + n.args.card.id, `commissar-${this.player_id}`);
      this._handCounters[this.player_id].incValue(-1);
    },

    notif_revealCommissarCard(n) {
      debug('Notif: revealing someone commissar card', n);

      if (n.args.player_id == this.player_id) {
        this.slide(`card-${n.args.card.id}`, `in-play-${this.player_id}`);
      } else {
        let c = $(`commissar-${n.args.player_id}`).querySelector('.m44-card');
        this.slide(c, `in-play-${n.args.player_id}`, {
          destroy: true,
        }).then(() => {
          this.addCard(n.args.card, `in-play-${n.args.player_id}`);
        });
      }
    },

    onEnteringStatePlayCommissarCard(args) {
      let cardId = args.cardId;
      $(`card-${cardId}`).classList.add('choice');

      let sections = {
        0: _('Left'),
        1: _('Central'),
        2: _('Right'),
      };
      if (args.sections) {
        args.sections.forEach((section) => {
          this.addPrimaryActionButton(`btnSection-${section}`, sections[section], () =>
            this.takeAction('actPlayCommissarCard', { section }),
          );
        });
      }
      if (args.canHill317) {
        this.addPrimaryActionButton(`btnCommissarPlay317`, _('Play it as an air power'), () =>
          this.takeAction('actPlayCommissarCard', { hill317: true }),
        );
      }
    },
  });
});
