define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
  return declare('memoir.orderUnits', null, {
    //////////////////////////////////////
    //    ___  ____  ____  _____ ____
    //   / _ \|  _ \|  _ \| ____|  _ \
    //  | | | | |_) | | | |  _| | |_) |
    //  | |_| |  _ <| |_| | |___|  _ <
    //   \___/|_| \_\____/|_____|_| \_\
    //////////////////////////////////////

    onEnteringStateOrderUnits(args) {
      this.makeTroopsSelectable(
        args.troops,
        this.onClickTroopToOrder.bind(this),
        this.isUnitSelectable.bind(this),
        'activated',
      );

      this._selectedTroopsOnTheMove = [];
      this.addPrimaryActionButton('btnConfirmOrder', _('Confirm orders'), () => this.onClickConfirmOrders());
    },

    onClickTroopToOrder(troopId, pos, selected) {
      let troopIndex = this._selectedTroopsOnTheMove.findIndex((t) => t == troopId);
      let selectedOnTheMove = troopIndex !== -1;

      if (!selected && !selectedOnTheMove) {
        let minFilling = this.getMinFillingOfSections();
        // If this unit can be selected without using "on the move", then go for it !
        if (this.isUnitSelectable(troopId, pos, selected, minFilling, true)) {
          return true;
        }
        // Otherwise, must flag it as "on the move"
        else {
          this._selectedTroopsOnTheMove.push(troopId);
          $('unit-' + troopId).classList.add('activated', 'onTheMove');
        }
      } else if (selectedOnTheMove) {
        this._selectedTroopsOnTheMove.splice(troopIndex, 1);
        $('unit-' + troopId).classList.remove('activated', 'onTheMove');
        return false;
      } else {
        // Try to put it on the move if not full
        if (this._selectedTroopsOnTheMove.length < this.getArgs().nOnTheMove) {
          this._selectedTroopsOnTheMove.push(troopId);
          $('unit-' + troopId).classList.add('activated', 'onTheMove');
        }
        return true;
      }
    },

    isUnitSelectable(troopId, pos, selected, minFilling, ignoreOnTheMove = false) {
      // A selected unit can always be unselected
      if (selected) return true;
      // A "on the move" unit can always be unselected
      if (this._selectedTroopsOnTheMove.includes(troopId)) return true;
      // If there is still "on the move" units left, the unit can be selected
      if (!ignoreOnTheMove && this._selectedTroopsOnTheMove.length < this.getArgs().nOnTheMove) return true;
      // Try to find a section with still enough room
      let sections = this.getArgs().sections;
      for (let i = 0; i < 3; i++) {
        if (pos.sections.includes(i) && minFilling[i] < sections[i]) {
          return true;
        }
      }

      return false;
    },

    onClickConfirmOrders() {
      this.takeAction('actOrderUnits', {
        troopIds: this._selectedTroops.join(';'),
        troopOnTheMoveIds: this._selectedTroopsOnTheMove.join(';'),
      });
    },

    /////////////////////////////////
    //  __  __  _____     _______
    // |  \/  |/ _ \ \   / / ____|
    // | |\/| | | | \ \ / /|  _|
    // | |  | | |_| |\ V / | |___
    // |_|  |_|\___/  \_/  |_____|
    /////////////////////////////////

    onEnteringStateMoveUnits(args) {
      Object.keys(args.troops).forEach((troopId) => {
        this.onClick('unit-' + troopId, () => {
          this.clientState('moveUnitsChooseTarget', _('Select the destination hex'), {
            troopId,
            cells: args.troops[troopId],
          });
        });
      });

      this.addPrimaryActionButton('btnMoveUnitsDone', _('Done'), () => this.takeAction('actMoveUnitsDone'));
    },

    onEnteringStateMoveUnitsChooseTarget(args) {
      this.addCancelStateBtn();
      $('unit-' + args.troopId).classList.add('moving');
      args.cells.forEach((cell) => {
        let oCell = $(`cell-${cell.x}-${cell.y}`);
        this.onClick(oCell, () => debug(cell));
        oCell.classList.add('forMove');
      });
    },
  });
});
