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
      this.makeUnitsSelectable(
        args.units,
        this.onClickUnitToOrder.bind(this),
        this.isUnitSelectable.bind(this),
        'activated',
      );

      this._selectedUnitsOnTheMove = [];
      this.addPrimaryActionButton('btnConfirmOrder', _('Confirm orders'), () => this.onClickConfirmOrders());
    },

    onClickUnitToOrder(unitId, pos, selected) {
      let unitIndex = this._selectedUnitsOnTheMove.findIndex((t) => t == unitId);
      let selectedOnTheMove = unitIndex !== -1;

      if (!selected && !selectedOnTheMove) {
        let minFilling = this.getMinFillingOfSections();
        // If this unit can be selected without using "on the move", then go for it !
        if (this.isUnitSelectable(unitId, pos, selected, minFilling, true)) {
          return true;
        }
        // Otherwise, must flag it as "on the move"
        else {
          this._selectedUnitsOnTheMove.push(unitId);
          $('unit-' + unitId).classList.add('activated', 'onTheMove');
        }
      } else if (selectedOnTheMove) {
        this._selectedUnitsOnTheMove.splice(unitIndex, 1);
        $('unit-' + unitId).classList.remove('activated', 'onTheMove');
        return false;
      } else {
        // Try to put it on the move if not full
        if (this._selectedUnitsOnTheMove.length < this.getArgs().nOnTheMove) {
          this._selectedUnitsOnTheMove.push(unitId);
          $('unit-' + unitId).classList.add('activated', 'onTheMove');
        }
        return true;
      }
    },

    isUnitSelectable(unitId, pos, selected, minFilling, ignoreOnTheMove = false) {
      // A selected unit can always be unselected
      if (selected) return true;
      // A "on the move" unit can always be unselected
      if (this._selectedUnitsOnTheMove.includes(unitId)) return true;
      // If there is still "on the move" units left, the unit can be selected
      if (!ignoreOnTheMove && this._selectedUnitsOnTheMove.length < this.getArgs().nOnTheMove) return true;
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
        unitIds: this._selectedUnits.join(';'),
        unitOnTheMoveIds: this._selectedUnitsOnTheMove.join(';'),
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
      Object.keys(args.units).forEach((unitId) => {
        if (args.units[unitId].length == 0) {
          $('unit-' + unitId).classList.add('unselectableForMoving');
          return;
        }

        this.onClick('unit-' + unitId, () => {
          this.clientState('moveUnitsChooseTarget', _('Select the destination hex'), {
            unitId,
            cells: args.units[unitId],
          });
        });
      });

      this.addPrimaryActionButton('btnMoveUnitsDone', _('Done'), () => this.takeAction('actMoveUnitsDone'));
    },

    onEnteringStateMoveUnitsChooseTarget(args) {
      this.addCancelStateBtn();
      $('unit-' + args.unitId).classList.add('moving');
      args.cells.forEach((cell) => {
        let oCell = $(`cell-${cell.x}-${cell.y}`);
        this.onClick(oCell, () => this.takeAction('actMoveUnit', { unitId: args.unitId, x: cell.x, y: cell.y }));
        oCell.classList.add('forMove');
      });
    },

    notif_moveUnit(n) {
      debug('Notif: unit is moving', n);
      this.slide('unit-' + n.args.unitId, `cell-${n.args.x}-${n.args.y}`, { duration: 1100 });
    },

    //////////////////////////////////////////
    //    _  _____ _____  _    ____ _  __
    //    / \|_   _|_   _|/ \  / ___| |/ /
    //   / _ \ | |   | | / _ \| |   | ' /
    //  / ___ \| |   | |/ ___ \ |___| . \
    // /_/   \_\_|   |_/_/   \_\____|_|\_\
    //////////////////////////////////////////

    onEnteringStateAttackUnits(args) {
      Object.keys(args.units).forEach((unitId) => {
        if (args.units[unitId].length == 0) {
          $('unit-' + unitId).classList.add('unselectableForAttacking');
          return;
        }

        this.onClick('unit-' + unitId, () => {
          this.clientState('attackUnitsChooseTarget', _('Select the target'), {
            unitId,
            cells: args.units[unitId],
          });
        });
      });

      this.addPrimaryActionButton('btnAttackUnitsDone', _('Done'), () => this.takeAction('actAttackUnitsDone'));
    },

    onEnteringStateAttackUnitsChooseTarget(args) {
      this.addCancelStateBtn();
      $('unit-' + args.unitId).classList.add('attacking');
      args.cells.forEach((cell) => {
        let oCell = $(`cell-${cell.x}-${cell.y}`);
        this.onClick(oCell, () => {
          this.clientState('debugPath', _('test'), { cell });
        }); //this.takeAction('actMoveUnit', { unitId: args.unitId, x: cell.x, y: cell.y }));
        oCell.classList.add('forAttack');
      });

      let source = $('unit-' + args.unitId).parentNode.parentNode;
      dojo.query('#m44-board .hex-cell-container').forEach((cell) => {
        this.connect(cell, 'mouseenter', () => {
          this.updateLineOfSight(source, cell);
        });
      });
      $('m44-board').classList.add('displayLineOfSight');
      this.updateLineOfSight(source, source);
    },

    onEnteringStateDebugPath(args) {
      this.addCancelStateBtn();
      args.cell.path.forEach((cell) => {
        let oCell = $(`cell-${cell.x}-${cell.y}`);
        oCell.classList.add('forAttack', 'selectable');
      });
    },

    updateLineOfSight(source, target) {
      let x1 = parseInt(source.style.gridColumnStart) + 1;
      let y1 = parseInt(source.style.gridRowStart) + 2;
      let x2 = parseInt(target.style.gridColumnStart) + 1;
      let y2 = parseInt(target.style.gridRowStart) + 2;
      $('lineOfSight').style.gridArea = y1 + ' / ' + x1 + ' / ' + y2 + ' / ' + x2;
      $('lineOfSight').classList.toggle('antidiagonal', (x2 - x1) * (y2 - y1) > 0);
      $('lineOfSight').classList.toggle('horizontal', y1 == y2);
      $('lineOfSight').classList.toggle('vertical', x1 == x2);
    },
  });
});
