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

      this.forEachPlayer((player) => {
        player.cards.forEach((card) => this.addCard(card, 'hand'));
      });
    },

    addCard(card, container) {
      let tplName = 'tplTacticCard';
      if (card.type < 10) tplName = 'tplSectionCard';
      this.place(tplName, card, container);
      this.addCustomTooltip('card-' + card.id, this[tplName](card, true));
    },

    tplTacticCard(card, tooltip = false) {
      this.formatDesc(card);
      if (card.value != 0) {
        card.type += card.value;
      }

      return `
        <div id='card-${card.id + (tooltip ? '-tooltip' : '')}' class='tactic-card' data-type='${card.type}'>
          <div class='card-resizable'>
            <div class='card-title'>${_(card.name)}</div>
            <div class='card-text-container'>
              <div class='card-text'>${card.desc}</div>
            </div>
          </div>
        </div>
      `;
    },

    tplSectionCard(card, tooltip = false) {
      return `
        <div id='card-${card.id + (tooltip ? '-tooltip' : '')}' class='section-card' data-type='${
        card.type
      }' data-value='${card.value}'>
          <div class='card-resizable'>
            <div class='card-title'>${_(card.name)}</div>
            <div class='card-subtitle'>${_(card.subtitle)}</div>
            <div class='card-illustration'></div>
            <div class='card-text-container'>
              <div class='card-text'>${_(card.text)}</div>
            </div>
          </div>
        </div>
      `;
    },

    formatDesc(card) {
      card.desc = card.text.map((t) => _(t)).join('<br />');
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
  });
});
