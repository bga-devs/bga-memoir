define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
  const MEDAL_ELIMINATION = 1;
  const MEDAL_POSITION = 2;

  return declare('memoir.players', null, {
    getPlayerColor(pId) {
      return this.gamedatas.players[pId].color;
    },

    setupPlayers() {
      // Basic UI tweaking
      this.forEachPlayer((player) => {
        let pos = player.id == this._pId ? 'bottom' : 'top';
        dojo.place('overall_player_board_' + player.id, pos + '-player');

        if (player.id == this.player_id) {
          player.cards.forEach((card) => this.addCard(card, pos + '-player-hand'));
        } else {
          // Add fakeCard
          let inPlay = player.inplay ? 1 : 0;
          for (let i = 0; i < player.cardsCount - inPlay; i++) {
            this.addCardBack(pos + '-player-hand');
          }
        }

        if (player.inplay) {
          this.addCard(player.inplay, pos + '-player-hand');
        }
      });
      dojo.place('right-side', 'm44-central-part');
    },

    setupTeams() {
      this.gamedatas.teams.forEach((team) => {
        let pos = this._bottomTeam == team.team ? 'bottom' : 'top';
        for (let i = 0; i < team.victory; i++) {
          dojo.place('<div class="m44-medal-slot"></div>', pos + '-medals-slots');
        }

        Object.values(team.medals).forEach((medal) => this.addMedal(medal));
      });
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
      if (medal.type == MEDAL_ELIMINATION) {
        content = `<div class="m44-unit" data-type="${medal.unit_type}" data-nation="${medal.unit_nation}" data-orientation="0">
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
        `<div id='card-${card.id + (tooltip ? '-tooltip' : '')}' class='${className}' data-type='${card.asset}'>
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
      let canHill317 = args._private.canHill317;
      Object.keys(cards).forEach((cardId) => {
        this.onClick(`card-${cardId}`, () => {
          if (cards[cardId]) {
            this.clientState('playCardSelectSection', _('Choose target section'), { cardId, sections: cards[cardId] });
          } else if (canHill317 && cards317[cardId] == true) {
            this.clientState('playCardHill317', _('Do you wish to play it as Air Power card?'), { cardId });
          } else {
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
      let section = null;
      this.addCancelStateBtn();
      this.addPrimaryActionButton(`btnHillYes`, _('Yes'), () =>
        this.takeAction('actPlayCard', { cardId, section, hill317: true }),
      );
      hill = false;

      this.addPrimaryActionButton(`btnHillNo`, _('No'), () =>
        this.takeAction('actPlayCard', { cardId, section, hill317: false }),
      );
    },

    notif_playCard(n) {
      debug('Notif: playing a card', n);
      if (this.player_id == n.args.player_id) {
        $('card-' + n.args.card.id).classList.add('inplay');
        //        this.slide('card-' + n.args.card.id, 'inplay');
      } else {
        let target = $('top-player-hand').querySelector('[data-type="-1"]:last-of-type');
        this.addCard(n.args.card, 'top-player-hand');
        this.flipAndReplace(target, $('card-' + n.args.card.id));
      }
    },

    notif_discardCard(n) {
      debug('Notif: discarding a card', n);
      if (!$('card-' + n.args.card.id)) {
        this.addCard(n.args.card, 'top-player-hand');
      }
      this.slide('card-' + n.args.card.id, 'discard', { duration: 1100 });
    },

    notif_discardCards(n) {
      // TODO
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
      if (this.player_id == n.args.player_id) return;
      for (let i = 0; i < n.args.nb; i++) {
        let card = this.addCardBack('deck');
        this.slide(card, 'top-player-hand');
      }
      this._deckCounter.incValue(-n.args.nb);
    },

    notif_pDrawCards(n) {
      debug('Notif: you are drawing card(s)', n);
      n.args.cards.forEach((card) => {
        this.addCard(card, 'deck');
        this.slide('card-' + card.id, 'bottom-player-hand');
      });
      this._deckCounter.incValue(-n.args.cards.length);
    },

    // Recon
    onEnteringStateDrawChoice(args) {
      // TODO : handle the case for nKeep > 1
      Object.values(args._private.cards).forEach((card) => {
        if (!$('card-' + card.id)) {
          this.addCard(card, 'bottom-player-hand');
        }

        $('card-' + card.id).classList.add('choice');
        this.onClick(`card-${card.id}`, () => this.takeAction('actChooseCard', { cardId: card.id }));
      });
    },
  });
});
