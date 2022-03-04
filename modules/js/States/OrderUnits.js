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
      this.removeClassNameOfCells('unselectableForAttacking');
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
      let nOnTheMove = this.getArgs().nOnTheMove || 0;
      if (!ignoreOnTheMove && this._selectedUnitsOnTheMove.length < nOnTheMove) return true;
      // Have we reached max number of selected unit ?
      if (this._selectedUnits.length == this.getArgs().n) return false;
      // Try to find a section with still enough room
      let sections = this.getArgs().sections;
      if (!sections) return true; // No section restriction => all good!
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

    onEnteringStateMoveUnits(args, excludeUnit = null) {
      debug(args);
      let nonEmptyUnits = [];
      // When a unit is clicked => prompt for the cell to move
      let callback = (unitId) => {
        let msg =
          nonEmptyUnits.length > 1
            ? _('Select the destination hex or another unit you want to move')
            : _('Select the destination hex');
        this.clientState('moveUnitsChooseTarget', msg, {
          unitId,
          cells: args.units[unitId],
        });
      };

      Object.keys(args.units).forEach((unitId) => {
        if (excludeUnit == unitId) return;
        if (args.units[unitId].length == 0) {
          $('unit-' + unitId).classList.add('unselectableForMoving');
          return;
        }

        nonEmptyUnits.push(unitId);
        this.onClick('unit-' + unitId, () => callback(unitId));
      });

      if (excludeUnit == null && nonEmptyUnits.length == 1) {
        callback(nonEmptyUnits[0]);
      }

      this.addPrimaryActionButton('btnMoveUnitsDone', _('Moves Done'), () => this.takeAction('actMoveUnitsDone'));

      // Auto select if a unit was partially moved
      let unitId = args.lastUnitMoved;
      if (excludeUnit == null && unitId != -1 && args.units[unitId] && args.units[unitId].length > 0) {
        callback(unitId);
      }
    },

    onEnteringStateMoveUnitsChooseTarget(args) {
      $('unit-' + args.unitId).classList.add('moving');
      args.cells.forEach((cell) => {
        let oCell = $(`cell-${cell.x}-${cell.y}`);
        this.onClick(oCell, () => this.takeAction('actMoveUnit', { unitId: args.unitId, x: cell.x, y: cell.y }));
        oCell.classList.add(cell.canAttack ? 'forMoveAndAttack' : 'forMove');
      });

      // Makes other units selectable
      this.onEnteringStateMoveUnits(this.last_server_state.args, args.unitId);
    },

    notif_moveUnit(n) {
      debug('Notif: unit is moving', n);
      this.slide('unit-' + n.args.unitId, `cell-${n.args.x}-${n.args.y}`, { duration: 580, preserveSize: true });
    },

    //////////////////////////////////////////
    //    _  _____ _____  _    ____ _  __
    //    / \|_   _|_   _|/ \  / ___| |/ /
    //   / _ \ | |   | | / _ \| |   | ' /
    //  / ___ \| |   | |/ ___ \ |___| . \
    // /_/   \_\_|   |_/_/   \_\____|_|\_\
    //////////////////////////////////////////

    onEnteringStateAttackUnits(args, excludeUnit = null) {
      this.removeClassNameOfCells('unselectableForMoving');
      let nonEmptyUnits = [];

      // When a unit is clicked => prompt for the target
      let callback = (unitId) => {
        let msg =
          nonEmptyUnits.length > 1
            ? _('Select the target or another unit you want to battle with')
            : _('Select the target');
        this.clientState('attackUnitsChooseTarget', msg, {
          unitId,
          cells: args.units[unitId],
        });
      };
      Object.keys(args.units).forEach((unitId) => {
        if (unitId == excludeUnit) return;
        if (args.units[unitId].length == 0) {
          $('unit-' + unitId).classList.add('unselectableForAttacking');
          return;
        }

        nonEmptyUnits.push(unitId);
        this.onClick('unit-' + unitId, () => callback(unitId));
      });
      if (excludeUnit == null && nonEmptyUnits.length == 1) {
        callback(nonEmptyUnits[0]);
      }

      this.addPrimaryActionButton('btnAttackUnitsDone', _('Attacks Done'), () => this.takeAction('actAttackUnitsDone'));

      // Auto select if a unit was partially moved
      let unitId = args.lastUnitAttacker;
      if (excludeUnit == null && unitId != -1 && args.units[unitId] && args.units[unitId].length > 0) {
        callback(unitId);
      }
    },

    onEnteringStateAttackUnitsChooseTarget(args) {
      this.addCancelStateBtn();
      $('unit-' + args.unitId).classList.add('attacking');
      args.cells.forEach((mixed) => {
        // Mixed can either be a cell or an action (eg removing wire)
        if (mixed.type && mixed.type == 'action') {
          this.addPrimaryActionButton('btn' + mixed.action, _(mixed.desc), () =>
            this.takeAction(mixed.action, { unitId: args.unitId }),
          );
        } else {
          let cell = mixed;
          let oCell = $(`cell-${cell.x}-${cell.y}`);
          oCell.classList.add('forAttack');
          this.onClick(oCell, () => this.takeAction('actAttackUnit', { unitId: args.unitId, x: cell.x, y: cell.y }));

          // Dice icons
          for (let i = 0; i < cell.dice; i++) {
            dojo.place('<i class="dice-mini"></i>', oCell);
          }
        }
      });

      // Makes other units selectable
      this.onEnteringStateAttackUnits(this.last_server_state.args, args.unitId);

      // Line of sight visualization
      let source = $('unit-' + args.unitId).parentNode.parentNode;
      dojo.query('#m44-board .hex-cell-container').forEach((cell) => {
        this.connect(cell, 'mouseenter', () => {
          this.updateLineOfSight(source, cell);
        });
      });
      $('m44-board').classList.add('displayLineOfSight');
      this.updateLineOfSight(source, source);
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

    ///////////////////////////////////////////////////
    //    ____  _____ _____ ____  _____    _  _____
    //   |  _ \| ____|_   _|  _ \| ____|  / \|_   _|
    //   | |_) |  _|   | | | |_) |  _|   / _ \ | |
    //   |  _ <| |___  | | |  _ <| |___ / ___ \| |
    //   |_| \_\_____| |_| |_| \_\_____/_/   \_\_|
    ///////////////////////////////////////////////////
    onEnteringStateAttackRetreat(args) {
      $('unit-' + args.unitId).classList.add('retreating');
      if (!this.isCurrentPlayerActive()) return;

      args.cells.forEach((cell) => {
        let oCell = $(`cell-${cell.x}-${cell.y}`);
        oCell.classList.add('forRetreat');
        this.onClick(oCell, () => this.takeAction('actRetreatUnit', { x: cell.x, y: cell.y }));
      });

      if (args.titleSuffix == 'skippable') {
        this.addPrimaryActionButton('btnRetreatUnitDone', _('Retreat Done'), () =>
          this.takeAction('actRetreatUnitDone'),
        );
      }
    },

    ////////////////////////////////////////////////////////////////
    //  _____     _           ____                           _
    // |_   _|_ _| | _____   / ___|_ __ ___  _   _ _ __   __| |
    //   | |/ _` | |/ / _ \ | |  _| '__/ _ \| | | | '_ \ / _` |
    //   | | (_| |   <  __/ | |_| | | | (_) | |_| | | | | (_| |
    //   |_|\__,_|_|\_\___|  \____|_|  \___/ \__,_|_| |_|\__,_|
    ///////////////////////////////////////////////////////////////
    onEnteringStateTakeGround(args) {
      $('unit-' + args.unitId).classList.add('attacking');
      if (!this.isCurrentPlayerActive()) return;

      this.addDangerActionButton('btnPassTakeGround', _('Pass'), () => this.takeAction('actPassTakeGround'));
      this.addPrimaryActionButton('btnTakeGround', _('Take Ground'), () => this.takeAction('actTakeGround'));
      let oCell = $(`cell-${args.cell.x}-${args.cell.y}`);
      oCell.classList.add('forAttack');
      this.onClick(oCell, () => this.takeAction('actTakeGround'));
    },

    ////////////////////////////////////////////////
    //     _              _               _
    //    / \   _ __ ___ | |__  _   _ ___| |__
    //   / _ \ | '_ ` _ \| '_ \| | | / __| '_ \
    //  / ___ \| | | | | | |_) | |_| \__ \ | | |
    // /_/   \_\_| |_| |_|_.__/ \__,_|___/_| |_|
    ////////////////////////////////////////////////
    onEnteringStateOpponentAmbush(args) {
      if (args['_private']['cards'].length != 0) {
        this.addActionButton('btnPlayAmbush', _('Ambush player'), () => this.takeAction('actAmbush'));
      }

      this.addDangerActionButton('btnPassAmbush', _('Pass'), () => this.takeAction('actPassAmbush'));
    },

    onEnteringStateAmbushResolve(args) {
      this.onEnteringStateAttackRetreat(args);
    },

    ////////////////////////////////////////////////////////////
    //  _____ _                 _     _   _
    // |  ___(_)_ __   ___  ___| |_  | | | | ___  _   _ _ __
    // | |_  | | '_ \ / _ \/ __| __| | |_| |/ _ \| | | | '__|
    // |  _| | | | | |  __/\__ \ |_  |  _  | (_) | |_| | |
    // |_|   |_|_| |_|\___||___/\__| |_| |_|\___/ \__,_|_|
    //////////////////////////////////////////////////////////

    onEnteringStateOrderUnitsFinestHour(args) {
      this.removeClassNameOfCells('unselectableForAttacking');
      this.makeUnitsSelectable(
        args.units,
        this.onClickUnitToOrderFinestHour.bind(this),
        this.isUnitSelectableFinestHour.bind(this),
        'activated',
      );

      this.addPrimaryActionButton('btnConfirmOrder', _('Confirm orders'), () => {
        this.takeAction('actOrderUnitsFinestHour', {
          unitIds: this._selectedUnits.join(';'),
        });
      });
    },

    onClickUnitToOrderFinestHour(unitId, pos, selected) {
      return true;
    },

    isUnitSelectableFinestHour(unitId, pos, selected, minFilling, ignoreOnTheMove = false) {
      // A selected unit can always be unselected
      if (selected) return true;
      // Compute selected units by type
      let t = [0, 0, 0];
      this._selectedUnits.forEach((unitId) => t[this.getArgs().units[unitId] - 1]++);
      // Compute remaining jokers
      let dice = this.getArgs().results;
      let remainingJokers =
        dice[2] - t[2] - (t[0] <= dice[0] ? 0 : t[0] - dice[0]) - (t[1] <= dice[1] ? 0 : t[1] - dice[1]);
      // Ok if enough remaining unit of this type of enough jokers left
      let type = this.getArgs().units[unitId] - 1;
      return t[type] < dice[type] || remainingJokers > 0;
    },
  });
});
