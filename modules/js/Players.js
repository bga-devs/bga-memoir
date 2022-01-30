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
      let tplName = 'tplSectionCard';
      this.place(tplName, card, container);
      // TODO : add tooltip
    },

    tplSectionCard(card) {
      return `
        <div id='card-${card.id}' class='section-card' data-type='${card.type}' data-value='${card.value}'>
          <div class='card-title'>${_(card.name)}</div>
          <div class='card-subtitle'>${_(card.subtitle)}</div>
          <div class='card-illustration'></div>
          <div class='card-text'>${_(card.text)}</div>
        </div>
      `;
    },
  });
});
