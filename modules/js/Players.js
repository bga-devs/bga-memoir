define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
  return declare('memoir.players', null, {
    getPlayerColor(pId) {
      return this.gamedatas.players[pId].color;
    },

    setupPlayers() {
      // Basic UI tweaking
      let pId = this.isSpectator ? Object.values(this.gamedatas.players)[0] : this.player_id;
      this.forEachPlayer((player) => {
        dojo.place('overall_player_board_' + player.id, player.id == pId ? 'bottom-player' : 'top-player');
      });
      dojo.place('right-side', 'm44-central-part');

      // TODO : handle other players
      this.forEachPlayer((player) => {
        player.cards.forEach((card) => this.addCard(card, 'hand'));
        if (player.inplay) {
          this.addCard(player.inplay, 'inplay');
        }
      });
    },

    addCard(card, container) {
      this.place('tplMemoirCard', card, container);
      this.addCustomTooltip('card-' + card.id, this.tplMemoirCard(card, true));
    },

    tplMemoirCard(card, tooltip = false) {
      let isSection = card.type < 20;
      this.formatDesc(card);
      card.asset = card.type + parseInt(card.value);
      let className = isSection ? 'section-card' : 'tactic-card';

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
        this.slide('card-' + n.args.card.id, 'inplay');
      } else {
        // TODO
        // this.addCard()
      }
    },
  });
});
