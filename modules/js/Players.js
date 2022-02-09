define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
  return declare('memoir.players', null, {
    getPlayerColor(pId) {
      return this.gamedatas.players[pId].color;
    },

    setupPlayers() {
      // Basic UI tweaking
      let pId = this.isSpectator ? Object.values(this.gamedatas.players)[0] : this.player_id;
      this.forEachPlayer((player) => {
        let pos = player.id == pId ? 'bottom' : 'top';
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

      this.place('tplMemoirCard', card, container);
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
        `
        <div id='card-${card.id + (tooltip ? '-tooltip' : '')}' class='${className}' data-type='${card.asset}'>
          <div class='card-resizable'>
            <div class='card-title'>${_(card.name)}</div> ` +
        (isSection ? `<div class='card-subtitle'>${_(card.subtitle)}</div>` : '') +
        `
            <div class='card-text-container'>
              <div class='card-text'>${card.desc}</div>
            </div>
          </div>
        </div>
      `
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
      args._private.cardIds.forEach((cardId) => {
        this.onClick('card-' + cardId, () => this.takeAction('actPlayCard', { cardId }));
      });
    },

    notif_playCard(n) {
      debug('Notif: playing a card', n);
      if (this.player_id == n.args.player_id) {
        $('card-' + n.args.card.id).classList.add('inplay');
//        this.slide('card-' + n.args.card.id, 'inplay');
      } else {
        // TODO
        // this.addCard()
      }
    },
  });
});
