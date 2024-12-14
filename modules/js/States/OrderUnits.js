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
      console.log('On entering Order State', args.units);
      this.makeUnitsSelectable(
        args.units,
        this.onClickUnitToOrder.bind(this),
        this.isUnitSelectable.bind(this),
        'activated',
        this.updateOrderUnitsBtn.bind(this),
      );

      this._selectedUnitsOnTheMove = [];
      this.updateOrderUnitsBtn();
    },

    updateOrderUnitsBtn() {
      dojo.destroy('btnConfirmOrder');
      if (this._selectedUnits.length > 0) {
        this.addPrimaryActionButton('btnConfirmOrder', _('Confirm orders'), () => this.onClickConfirmOrders());
      } else {
        this.addDangerActionButton('btnConfirmOrder', _('Confirm no order and end your turn'), () =>
          this.onClickConfirmOrders(),
        );
      }
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
      let marineCommand = this.getArgs().marineCommand || false;
      let extra = false;
      for (let i = 0; i < 3; i++) {
        if (pos.sections.includes(i) && minFilling[i] < sections[i]) {
          return true;
        }

        if (minFilling[i] > sections[i]) {
          extra = true;
        }
      }

      // Allow one extra unit
      if (!extra && marineCommand) {
        return true;
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
      let nonEmptyUnits = [];
      this.removeClassNameOfCells('unselectableForAttacking');
      this.removeClassNameOfCells('unselectableForMoving');

      // When a unit is clicked => prompt for the cell to move
      let callback = (unitId) => {
        let msg =
          nonEmptyUnits.length > 1
            ? _('Select the destination hex or another unit you want to move')
            : _('Select the destination hex');
        this.clientState('moveUnitsChooseTarget', msg, {
          unitId,
          cells: args.units[unitId],
          actionCount: args.actionCount,
        });
      };

      Object.keys(args.units).forEach((unitId) => {
        if (excludeUnit == unitId) return;
        if (args.units[unitId].length <= 1) {
          $('unit-' + unitId).classList.add('unselectableForMoving');
          if (args.units[unitId][0].canAttack) {
            $('unit-' + unitId).classList.add('mayAttack');
          }
          return;
        }

        nonEmptyUnits.push(unitId);
        this.onClick('unit-' + unitId, () => callback(unitId));
      });

      if (excludeUnit == null && nonEmptyUnits.length == 1) {
        callback(nonEmptyUnits[0]);
      }

      this.addPrimaryActionButton('btnMoveUnitsDone', _('End all unit movements'), () =>
        this.takeAction('actMoveUnitsDone'),
      );

      // Auto select if a unit was partially moved
      let unitId = args.lastUnitMoved;
      if (excludeUnit == null && unitId != -1 && args.units[unitId] && args.units[unitId].length > 1) {
        callback(unitId);
      }
    },

    onEnteringStateMoveUnitsChooseTarget(args) {
      $('unit-' + args.unitId).classList.add('moving', 'selected');
      args.cells.forEach((cell) => {
        // Mixed can either be a cell or an action (eg removing wire)
        if (cell.type && cell.type == 'action') {
          this.addPrimaryActionButton('btn' + cell.action, _(cell.desc), () =>
            this.takeAction(cell.action, { unitId: args.unitId }),
          );
        } else {
          let oCell = $(`cell-${cell.x}-${cell.y}`);
          if (!cell.source) {
            this.onClick(oCell, () => this.takeAction('actMoveUnit', { unitId: args.unitId, x: cell.x, y: cell.y }));
          }
          if (!cell.source) {
            oCell.classList.add(cell.canAttack ? 'forMoveAndAttack' : 'forMove'); 
          }


          if (cell.stop) {
            dojo.place(`<div class='mustStop'></div>`, oCell);
          }
          if (cell.noAttack) {
            dojo.place(`<div class='cannotAttack'></div>`, oCell);
          }
        }
      });

      // Makes other units selectable
      this.onEnteringStateMoveUnits(this.last_server_state.args, args.unitId);
    },

    notif_moveUnit(n) {
      debug('Notif: unit is moving', n);
      this.clearPossible();
      $('unit-' + n.args.unitId).classList.add('moving');
      $('unit-' + n.args.unitId).classList.remove('selected');
      this.slide('unit-' + n.args.unitId, `cell-${n.args.x}-${n.args.y}`, { duration: 580, preserveSize: true });
      this._grid[n.args.x][n.args.y].unit = this._grid[n.args.fromX][n.args.fromY].unit;
      this._grid[n.args.fromX][n.args.fromY].unit = null;
    },

    notif_moveUnitFromReserve(n) {
      debug('Notif: unit is moving from reserve area', n);
      this.clearPossible();
      // remove reserve token from staging area
      let unit = document.getElementById('unit-' + n.args.unitId);
      let parent = unit.parentElement;
      let tokenToRemove = parent.firstChild;
      tokenToRemove = parent.removeChild(tokenToRemove);
      // move unit
      $('unit-' + n.args.unitId).classList.add('moving');
      $('unit-' + n.args.unitId).classList.remove('selected');
      this.slide('unit-' + n.args.unitId, `cell-${n.args.x}-${n.args.y}`, { duration: 580, preserveSize: true });
      this._grid[n.args.x][n.args.y].unit = this._grid[n.args.fromX][n.args.fromY].unit;
      this._grid[n.args.fromX][n.args.fromY].unit = null;
    },

    onEnteringStateTrainReinforcement(args) { // args 1 valid neighbour cells of train
      args.forEach((cell) => {
        let oCell = $(`cell-${cell.x}-${cell.y}`);
        oCell.classList.add('forTrainReinforcement');
        this.onClick(oCell, () => this.takeAction('actTrainReinforcement', { x: cell.x, y: cell.y }));
      });
    },
      


    //////////////////////////////////////////
    //    _  _____ _____  _    ____ _  __
    //    / \|_   _|_   _|/ \  / ___| |/ /
    //   / _ \ | |   | | / _ \| |   | ' /
    //  / ___ \| |   | |/ ___ \ |___| . \
    // /_/   \_\_|   |_/_/   \_\____|_|\_\
    //////////////////////////////////////////

    onEnteringStateAttackUnits(args, excludeUnit = null, btn = true) {
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
          btn: btn,
          actionCount: args.actionCount,
        });
      };
      Object.keys(args.units).forEach((unitId) => {
        if (unitId == excludeUnit) return;
        if (args.units[unitId].length == 0) {
          $('unit-' + unitId).classList.add('unselectableForAttacking');
          return;
        }
        if (this.isCurrentPlayerActive()) {
          nonEmptyUnits.push(unitId);
          this.onClick('unit-' + unitId, () => callback(unitId));
        }
      });
      if (!this.isCurrentPlayerActive()) {
        return;
      }
      if (excludeUnit == null && nonEmptyUnits.length == 1) {
        callback(nonEmptyUnits[0]);
      }

      if (btn == true) {
        this.addPrimaryActionButton('btnAttackUnitsDone', _('End all unit attacks'), () => {
          if (nonEmptyUnits.length > 0) {
            this.confirmationDialog(_('Are you sure you want to want to skip your remaining attacks?'), () => {
              this.takeAction('actAttackUnitsDone');
            });
          } else {
            this.takeAction('actAttackUnitsDone');
          }
        });
      }

      // Auto select if a unit was partially moved
      let unitId = args.lastUnitAttacker;
      if (excludeUnit == null && unitId != -1 && args.units[unitId] && args.units[unitId].length > 0) {
        callback(unitId);
      }
    },

    onEnteringStateAttackUnitsChooseTarget(args) {
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
      this.onEnteringStateAttackUnits(this.last_server_state.args, args.unitId, args.btn);

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

    onEnteringStateBattleBack(args) {
      $('unit-' + args.unitId).classList.add('attacking');
      let oCell = $(`cell-${args.cell.x}-${args.cell.y}`);
      oCell.classList.add('forAttack');
      oCell.classList.add('selectable');

      this.addPrimaryActionButton('btnBattleBack', _('Battle back'), () => this.takeAction('actBattleBack'));
      this.addPrimaryActionButton('btnBattleBackPass', _('Pass'), () => this.takeAction('actBattleBackPass'));
    },

    notif_throwAttack(n) {
      debug('Someone throws an attack', n);
      let target = $(`unit-${n.args.oppUnitId}`);
      target.classList.add('attacked');

      if (n.args.unitId) {
        let source = $(`unit-${n.args.unitId}`);
        source.classList.add('attacking');

        $('m44-board').classList.add('displayLineOfSightAttack');
        this.updateLineOfSight(source.parentNode.parentNode, target.parentNode.parentNode);
      }
    },

    ///////////////////////////////////////////////////
    //    ____  _____ _____ ____  _____    _  _____
    //   |  _ \| ____|_   _|  _ \| ____|  / \|_   _|
    //   | |_) |  _|   | | | |_) |  _|   / _ \ | |
    //   |  _ <| |___  | | |  _ <| |___ / ___ \| |
    //   |_| \_\_____| |_| |_| \_\_____/_/   \_\_|
    ///////////////////////////////////////////////////
    onEnteringStateAttackRetreat(args) {
      // if unit was killed in action
      if (!$('unit-' + args.unitId)) {
        return;
      }

      $('unit-' + args.unitId).classList.add('retreating');
      if (args.attackingUnit != -1) {
        $('unit-' + args.attackingUnit).classList.add('attacking');
      }
      Object.keys(args.attackUnits).forEach((unitId) => {
        if (args.attackingUnit == unitId) return;

        if (args.attackUnits[unitId].length == 0) {
          $('unit-' + unitId).classList.add('unselectableForAttacking');
        } else {
          $('unit-' + unitId).classList.add('mayAttack');
        }
      });

      if (!this.isCurrentPlayerActive()) return;

      args.cells.forEach((cell) => {
        let oCell = $(`cell-${cell.x}-${cell.y}`);
        oCell.classList.add('forRetreat');
        this.onClick(oCell, () => this.takeAction('actRetreatUnit', { x: cell.x, y: cell.y }));
      });

      if (args.min == 0) {
        this.addPrimaryActionButton('btnRetreatUnitDone', _('End unit retreat'), () =>
          this.takeAction('actRetreatUnitDone'),
        );
      }

      if (args.min < args.max) {
        this.addPrimaryActionButton('btnRetreatIgnore1Flag', _('Ignore 1 Flag'), () =>
          this.takeAction('actIgnore1Flag'),
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
      if (args.unitId != -1) {
        $('unit-' + args.unitId).classList.add('attacking');
      }
      if (!this.isCurrentPlayerActive()) return;

      this.addDangerActionButton('btnPassTakeGround', _('Pass'), () => this.takeAction('actPassTakeGround'));
      this.addPrimaryActionButton('btnTakeGround', _('Take Ground'), () => this.takeAction('actTakeGround'));
      let oCell = $(`cell-${args.cell.x}-${args.cell.y}`);
      oCell.classList.add('forAttack');
      this.onClick(oCell, () => this.takeAction('actTakeGround'));
    },

    onEnteringStateArmorOverrun(args, excludeUnit = null) {
      this.onEnteringStateAttackUnits(args, excludeUnit, false);
      if (this.isCurrentPlayerActive()) {
        this.addPrimaryActionButton('btnOverrunDone', _('Do not attack'), () => this.takeAction('actNextAttack'));
      }
    },

    onEnteringStateDesertMove(args, excludeUnit = null) {
      this.onEnteringStateMoveUnits(args, excludeUnit);
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
        args['_private']['cards'].forEach((cardId) => {
          this.onClick(`card-${cardId}`, () => this.takeAction('actAmbush'));
        });
        this.addDangerActionButton('btnPassAmbush', _('Pass'), () => this.takeAction('actPassAmbush'));
      } else {
        this.changePageTitle('nooption');
        this.addDangerActionButton('btnPassAmbush', _('Pass'), () => this.takeAction('actPassAmbush'));
        this.addDangerActionButton('btnPassAmbushDontAskMeAgain', _('Auto-pass'), () => {
          this.setPreferenceValue(150, 1);
          this.takeAction('actPassAmbush');
        });
        this.addSecondaryActionButton(
          'btnPassAmbushHelper',
          `<div class='help-marker-btn'><svg><use href="#help-marker-svg" /></svg></div>`,
          () => this.tooltips['btnPassAmbushHelper'].open($('btnPassAmbushHelper')),
        );
        this.addCustomTooltip(
          'btnPassAmbushHelper',
          _(
            "Whenever a player is attacked in close combat, he might react to the attack if they have an Ambush card in hand. <br /> In order to not reveal any private information about the cards in your hand, you will be prompted every time you get in close combat even if you don't have that card in hand by default. You can change that by clicking the auto-pass button or in the settings available clicking on the gears next to your names.",
          ),
          400,
          true,
        );
      }
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
        () => true,
        this.isUnitSelectableFinestHour.bind(this),
        'activated',
        this.updateFinestHourOrderUnitsBtn.bind(this),
      );
      this.clearSelectedUnits();
      this.updateFinestHourOrderUnitsBtn();
    },

    updateFinestHourOrderUnitsBtn() {
      dojo.destroy('btnConfirmOrder');
      if (this._selectedUnits.length > 0) {
        this.addPrimaryActionButton('btnConfirmOrder', _('Confirm orders'), () =>
          this.onClickConfirmFinestHourOrders(),
        );
      } else {
        this.addDangerActionButton('btnConfirmOrder', _('Confirm no order and end your turn'), () =>
          this.onClickConfirmFinestHourOrders(),
        );
      }
    },

    onClickConfirmFinestHourOrders() {
      this.takeAction('actOrderUnitsFinestHour', {
        unitIds: this._selectedUnits.join(';'),
      });
    },

    isUnitSelectableFinestHour(unitId, pos, selected, minFilling) {
      // A selected unit can always be unselected
      if (selected) return true;
      // Compute selected units by type
      let t = [0, 0, 0];
      this._selectedUnits.forEach((unit2Id) => t[this.getArgs().units[unit2Id] - 1]++);
      // Compute remaining jokers
      let dice = this.getArgs().results;
      let usedJokers = t[2] + (t[0] <= dice[0] ? 0 : t[0] - dice[0]) + (t[1] <= dice[1] ? 0 : t[1] - dice[1]);
      t[2] = usedJokers;
      let remainingJokers = dice[2] - usedJokers;
      // Ok if enough remaining unit of this type of enough jokers left
      let type = this.getArgs().units[unitId] - 1;
      return t[type] < dice[type] || remainingJokers > 0;
    },

    ///////////////////////////////////
    //  __  __          _ _
    // |  \/  | ___  __| (_) ___ ___
    // | |\/| |/ _ \/ _` | |/ __/ __|
    // | |  | |  __/ (_| | | (__\__ \
    // |_|  |_|\___|\__,_|_|\___|___/
    ///////////////////////////////////

    onEnteringStateTargetMedics(args) {
      this.removeClassNameOfCells('unselectableForAttacking');
      this.makeUnitsSelectable(
        args.units,
        this.onClickUnitToOrder.bind(this),
        this.isUnitSelectable.bind(this),
        'activated',
        this.updateOrderMedicsBtn.bind(this),
      );
      this._selectedUnitsOnTheMove = [];

      this.updateOrderMedicsBtn();
      // args.unitIds.forEach((unitId) =>
      //   this.onClick(`unit-${unitId}`, () => this.takeAction('actTargetMedics', { unitId })),
      // );
    },

    updateOrderMedicsBtn() {
      dojo.destroy('btnConfirmOrder');
      if (this._selectedUnits.length > 0) {
        this.addPrimaryActionButton('btnConfirmOrder', _('Confirm orders'), () => this.onClickConfirmMedicOrders());
      } else {
        this.addDangerActionButton('btnConfirmOrder', _('Confirm no order and end your turn'), () =>
          this.onClickConfirmMedicOrders(),
        );
      }
    },

    onClickConfirmMedicOrders() {
      this.takeAction('actTargetMedics', {
        unitIds: this._selectedUnits.join(';'),
      });
    },

    notif_healUnit(n) {
      debug('Notif: healing a unit', n);
      let unit = $(`unit-${n.args.unitId}`);
      unit.dataset.figures = parseInt(unit.dataset.figures) + n.args.nb;
    },

    /////////////////////////////////////////////////
    //     _    _      ____
    //    / \  (_)_ __|  _ \ _____      _____ _ __
    //   / _ \ | | '__| |_) / _ \ \ /\ / / _ \ '__|
    //  / ___ \| | |  |  __/ (_) \ V  V /  __/ |
    // /_/   \_\_|_|  |_|   \___/ \_/\_/ \___|_|
    //
    /////////////////////////////////////////////////

    onEnteringStateTargetAirPower(args) {
      this.makeUnitsSelectable(
        args.units,
        this.onClickUnitTargetAirPower.bind(this),
        this.isUnitSelectableAirPower.bind(this),
        'airPowerTarget',
      );

      this.addPrimaryActionButton('btnConfirmTargetAirPower', _('Confirm target(s)'), () =>
        this.takeAction('actTargetAirPower', { unitIds: this._selectedUnits.join(';') }),
      );
    },

    onClickUnitTargetAirPower(unitId, pos, selected) {
      // Add a number on them
      if (!selected) {
        $(`unit-${unitId}`).dataset.airPowerOrder = this._selectedUnits.length + 1;
      }

      dojo.destroy('btnClearSelectedUnits');
      if (this._selectedUnits.length - (selected ? 1 : 0) > 0) {
        this.addSecondaryActionButton('btnClearSelectedUnits', _('Clear'), () => this.clearSelectedUnits());
      }
      return true;
    },

    isUnitSelectableAirPower(unitId, pos, selected, minFilling) {
      // If no selected unit yet, can select any unit
      if (this._selectedUnits.length == 0) return true;
      let lastUnitId = this._selectedUnits[this._selectedUnits.length - 1];
      // A selected unit can only be unselected if it's the last one
      if (selected) return unitId == lastUnitId;
      // No more than 4 units
      if (this._selectedUnits.length == 4) return false;
      // Otherwise, it must be adjacent to the last selected unit
      let lastUnitPos = this.getArgs()['units'][lastUnitId];
      return Math.abs(pos.x - lastUnitPos.x) + 2 * Math.abs(pos.y - lastUnitPos.y) <= 3;
    },

    /////////////////////////////////////////////
    //  ____
    // | __ )  __ _ _ __ _ __ __ _  __ _  ___
    // |  _ \ / _` | '__| '__/ _` |/ _` |/ _ \
    // | |_) | (_| | |  | | | (_| | (_| |  __/
    // |____/ \__,_|_|  |_|  \__,_|\__, |\___|
    //                             |___/
    /////////////////////////////////////////////

    onEnteringStateTargetBarrage(args) {
      args.unitIds.forEach((unitId) =>
        this.onClick(`unit-${unitId}`, () => this.takeAction('actTargetBarrage', { unitId })),
      );
    },

    ///////////////////////////////////////////
    //     _    _      ____
    //    / \  (_)_ __|  _ \ _ __ ___  _ __
    //   / _ \ | | '__| | | | '__/ _ \| '_ \
    //  / ___ \| | |  | |_| | | | (_) | |_) |
    // /_/   \_\_|_|  |____/|_|  \___/| .__/
    //                                |_|
    ///////////////////////////////////////////

    onEnteringStateAirDrop(args) {
      args.cells.forEach((cell) => {
        let oCell = $(`cell-${cell.x}-${cell.y}`);
        oCell.classList.add('forAirDrop');
        this.onClick(oCell, () => this.takeAction('actAirDrop', { x: cell.x, y: cell.y }));
      });
    },

    onEnteringStateAirDrop2(args) {
      args.cells.forEach((cell) => {
        let oCell = $(`cell-${cell.x}-${cell.y}`);
        oCell.classList.add('forAirDrop');
        this.onClick(oCell, () => this.takeAction('actAirDrop2', { x: cell.x, y: cell.y }));
      });
    },

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //______                               ______      _ _  ______           _                                       _   
    //| ___ \                              | ___ \    | | | |  _  \         | |                                     | |  
    //| |_/ /___  ___  ___ _ ____   _____  | |_/ /___ | | | | | | |___ _ __ | | ___  _   _  ___ _ __ ___   ___ _ __ | |_ 
    //|    // _ \/ __|/ _ \ '__\ \ / / _ \ |    // _ \| | | | | | / _ \ '_ \| |/ _ \| | | |/ _ \ '_ ` _ \ / _ \ '_ \| __|
    //| |\ \  __/\__ \  __/ |   \ V /  __/ | |\ \ (_) | | | | |/ /  __/ |_) | | (_) | |_| |  __/ | | | | |  __/ | | | |_ 
    //\_| \_\___||___/\___|_|    \_/ \___| \_| \_\___/|_|_| |___/ \___| .__/|_|\___/ \__, |\___|_| |_| |_|\___|_| |_|\__|
    //                                                                | |             __/ |                              
    //                                                                |_|            |___/  
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    onEnteringStateReserveUnitsDeployement(args) {
      console.log('reserverolldeployement args',args);

      this.clearActionButtons();
      var elem_map = new Array();
      elem_map['inf'] = ['1 infantry', 'unit'];
      elem_map['tank'] = ['1 armor', 'unit'];
      elem_map['gun'] = ['1 artillery', 'unit'];
      elem_map['inf2'] = ['1 special force infantry', 'unit'];
      elem_map['tank2'] = ['1 elite armor', 'unit'];
      elem_map['wild'] = ['1 unit of your choice', 'unit'];
      elem_map['wild2'] = ['1 elite unit of your choice', 'unit'];
      elem_map['sandbag'] = ['1 sandbag (at no token cost)', 'obstacle'];
      elem_map['wire'] = ['2 wires (at no token cost)', 'obstacle2'];
      elem_map['advance2'] = ['advance 1 unit by 2 hexes', 'moveunit']; // TO DO
      elem_map['airpowertoken'] = ['get 1 one air power token (at no cost)', 'token']; 
     
      let playerid = this.player_id;
      args = args[playerid];
      let element_list = Object.values(args.elements_to_deploy[playerid]);
      console.log(element_list,playerid);
      let n = 0;
      element_list.forEach( (elem) => {
        n++;
        console.log(elem);
        let elem_name = elem_map[elem][0];
        let elem_type = elem_map[elem][1];
        console.log(elem_name);
        switch(elem_type) {
          case 'unit':
            this.addPrimaryActionButton('btnReserveElem'+n, _(elem_name), () => this.onClickChooseDepLocation(elem, args));
          break;

          case 'obstacle':
            this.addPrimaryActionButton('btnReserveElem'+n, _(elem_name), () => this.onClickChooseSandbagLocation(elem, args));
          break;

          case 'obstacle2':
            this.addPrimaryActionButton('btnReserveElem'+n, _(elem_name), () => this.onClickSelectUnitForWire(elem, args));
          break;

          case 'token' :
            console.log('Case AirPower Token', playerid);
            this.addPrimaryActionButton('btnReserveElem'+n, _(elem_name), () => this.takeAction('actReserveUnitsDeployement', { x: 0, y: 0, 
              finished: false, 
              pId: playerid, 
              elem: elem,
              isWild: false,
              onStagingArea: true,
              unit_Id: 0,
              misc_args : {}
              }));
          break;

          case 'moveunit' :
            console.log('Case Advance 1 unit by 2 hexes');
            this.addPrimaryActionButton('btnReserveElem'+n, _(elem_name), () => this.onClickSelectUnitToBeMoved(elem, args));

          break;

          default:
          console.log('Sorry, unknow element type.', elem_type);
        }
      })
      
      this.addDangerActionButton('btnConfirmReserveDeployement', _('End reserve deployement'), () =>
        this.onClickConfirmReserveDeployement(args),
      );
    },

    onClickConfirmReserveDeployement(args) {
      let playerid = this.player_id;
      let elem = '';
      this.takeAction('actReserveUnitsDeployement', {
        x : 0, y: 0, 
        finished : true, 
        pId: playerid, 
        elem: elem,
        isWild: false,
        onStagingArea: false,
        unit_Id: 0,
        misc_args : {}
      });
    },

    onClickChooseDepLocation(elem, args) {
      console.log('elem', elem);
      let stagingArea = [];
      stagingArea = [...$('bottom-staging-area').getElementsByClassName('reserve-unit')];
      console.log('staging units area', stagingArea);
      let playerid = this.player_id;
      if (elem != 'wild' && elem != 'wild2') {
        stagingArea.forEach((area) => {
          area.classList.add('forReserveStagingDeploy');
          let finished = false;
          this.onClick(area, () => this.takeAction('actReserveUnitsDeployement', { x: -1, y: 0, 
              finished: finished, 
              pId: playerid, 
              elem: elem,
              isWild: false,
              onStagingArea: true,
              unit_Id: 0,
              misc_args : {}
              }));
        });
      }
      
      let cells_list = Object.values(args.cells_units_deployement);
      console.log('cells array', cells_list);
      //let playerid = args.playerid;
      if (elem != 'wild' && elem != 'wild2') {
        cells_list.forEach((cell) => {
          let oCell = $(`cell-${cell.x}-${cell.y}`);
          oCell.classList.add('forReserveUnitDeploy');
          let finished = false
          this.onClick(oCell, () => this.takeAction('actReserveUnitsDeployement', { x: cell.x, y: cell.y, 
            finished: finished, 
            pId: playerid, 
            elem: elem,
            isWild: false,
            onStagingArea: false,
            unit_Id : 0,
            misc_args : {}
          }));
        });
      }
      switch (elem) {
        case 'wild':
          this.clearActionButtons();
          this.addPrimaryActionButton('btnReserveElem'+10, _('1 infantry'), () => this.onClickChooseDepLocation2('inf', args));
          this.addPrimaryActionButton('btnReserveElem'+11, _('1 armor'), () => this.onClickChooseDepLocation2('tank', args));
          this.addPrimaryActionButton('btnReserveElem'+12, _('1 artillery'), () => this.onClickChooseDepLocation2('gun', args));

          break;
        case 'wild2':
          this.clearActionButtons();
          this.addPrimaryActionButton('btnReserveElem'+10, _('1 special force infantry'), () => this.onClickChooseDepLocation2('inf2', args));
          this.addPrimaryActionButton('btnReserveElem'+11, _('1 elite armor'), () => this.onClickChooseDepLocation2('tank2', args));
          this.addPrimaryActionButton('btnReserveElem'+12, _('1 artillery'), () => this.onClickChooseDepLocation2('gun', args));
          break;
        default:
          console.log(`Sorry, none wild unit to deploy.`);
      }

    },

    onClickChooseDepLocation2(elem, args) {
      console.log('elem', elem);
      let stagingArea = [];
      stagingArea = [...$('bottom-staging-area').getElementsByClassName('reserve-unit')];
      console.log('staging units area', stagingArea);
      let playerid2 = this.player_id;
      stagingArea.forEach((area) => {
        area.classList.add('forReserveStagingDeploy');
        let finished = false;
        this.onClick(area, () => this.takeAction('actReserveUnitsDeployement', { x: 0, y: 0, 
            finished: finished, 
            pId: playerid2, 
            elem: elem,
            isWild: true,
            onStagingArea: true,
            unit_Id : 0,
            misc_args : {}
            }));

      });
      let cells_list = Object.values(args.cells_units_deployement);
      console.log('cells array', cells_list);
      cells_list.forEach((cell) => {
        let oCell = $(`cell-${cell.x}-${cell.y}`);
        oCell.classList.add('forReserveUnitDeploy');
        let finished = false
        this.onClick(oCell, () => this.takeAction('actReserveUnitsDeployement', { x: cell.x, y: cell.y, 
          finished: finished, 
          pId: playerid2, 
          elem: elem,
          isWild : true,
          onStagingArea: false,
          unit_Id: 0,
          misc_args : {}
          }));
        });
      },

    onClickChooseSandbagLocation(elem, args) {
      console.log('elem', elem);
      let cells_list = Object.values(args.cells_sandbag_deployement);
      console.log('cells array', cells_list);
      let playerid = this.player_id;
      cells_list.forEach((cell) => {
        let oCell = $(`cell-${cell.x}-${cell.y}`);
        oCell.classList.add('forReserveUnitDeploy');
        let finished = false
        this.onClick(oCell, () => this.takeAction('actReserveUnitsDeployement', { x: cell.x, y: cell.y, 
          finished: finished, 
          pId: playerid, 
          elem: elem,
          isWild: false,
          onStagingArea: false,
          unit_Id: 0,
          misc_args : {}
          }));
        });
    },

    onClickSelectUnitForWire(elem, args) {
      console.log('elem', elem);
      let cells_list = Object.entries(args.cells_sandbag_deployement);
      console.log('cells array', cells_list);
      for (const [key, value] of cells_list) {
        let unitId = key;
        let cell = value;
        let oCell = $(`cell-${cell.x}-${cell.y}`);
        oCell.classList.add('forReserveUnitDeploy');
        this.onClick(oCell, () => this.onClickSelectWireLocation(elem, args, cell, unitId));
      };
    },

    onClickSelectWireLocation(elem,args,selectedUnitCell, unitId) {
      // First propose list of ReachableCells at Distance from php Args
      this.clearPossible();
      let playerid2 = this.player_id;
      args.wireCellList = new Object();
      this.addPrimaryActionButton('btnConfirmWireTargetCells', _('Confirm cells where to deploy 2 wire'), () => 
      {
        console.log(args.wireCellList);
        let cells_list = Object.entries(args.wireCellList);
        let wireCellList2 = JSON.stringify(args.wireCellList);
        console.log(wireCellList2);
        for (const [key, value] of cells_list) {
          let cell = value;
          $(`cell-${cell.x}-${cell.y}`).classList.remove('airPowerTarget');
        };
        this.takeAction('actReserveUnitsDeployement', { x: 0, y: 0, 
          finished: false, 
          pId: playerid2, 
          elem: elem,
          isWild: false,
          onStagingArea: false,
          unit_Id: 0,
          misc_args : wireCellList2
        });
        }
      );
      let cells_list = Object.values(args.cells_wire_deployement[unitId]);
      let playerid = this.player_id;
      cells_list.forEach((cell) => {
        let cell2 = [];
        let oCell = $(`cell-${cell.x}-${cell.y}`);
        oCell.classList.add('forReserveUnitDeploy');
        cell2['x'] = cell.x;
        cell2['y'] = cell.y;
        this.onClick(oCell, () => this.onClickCellTargetWire(elem, args, cell2));

        
      });

      // display and mark 2 empty and valid cells close to selected unit like Air power target
      // mark all reachable cells
    }, 

    onClickCellTargetWire(elem,args,cell) {
      // Add a number on selected cell where to add wires and add them in list to be returned to memoir.action.php
      console.log('Selected Wire Cell List', args.wireCellList, Object.keys(args.wireCellList).length)
      let length = Object.keys(args.wireCellList).length;
      cell1 = new Object();
      cell2 = new Object();
      cell2['x'] = cell.x;
      cell2['y'] = cell.y;
      if (length == 1) {
        cell1 = args.wireCellList[0];
      }
      if (length < 2 && !this.isCellObjectEqual(cell1,cell2)) {
        $(`cell-${cell.x}-${cell.y}`).dataset.airPowerOrder = length + 1;
        $(`cell-${cell.x}-${cell.y}`).classList.add('airPowerTarget');
        args.wireCellList[length] = cell2;
        console.log(args.wireCellList);
      }
    },

    isCellObjectEqual(cell1,cell2) {
      console.log('comparaison',cell1, cell2)
      return cell1['x'] == cell2['x'] && cell1['y'] == cell2['y'];
      // !Object.values(args.wireCellList).includes(cell2)
    },


    onClickSelectUnitToBeMoved(elem, args) {
      console.log('elem', elem);
      let cells_list = Object.entries(args.cells_sandbag_deployement);
      console.log('cells array', cells_list);
      let playerid = this.player_id;
      for (const [key, value] of cells_list) {
        let unitId = key;
        let cell = value;
        let oCell = $(`cell-${cell.x}-${cell.y}`);
        oCell.classList.add('forReserveUnitDeploy');
        this.onClick(oCell, () => this.onClickChooseMoveUnitLocation(elem, args, cell, unitId));
      };
    },

    onClickChooseMoveUnitLocation(elem,args,startingCell, unitId) {
      // First propose list of ReachableCells at Distance from php Args 
      //(Advance could also mean towards ennemy not back 'contrary of Retreat') -> Keep all direction as a first release
      // mark all reachable cells like in moveTrait
      this.clearPossible();
      let cells_list = Object.values(args.cells_advance2[unitId]);
      let playerid = this.player_id;
      cells_list.forEach((cell) => {
        let oCell = $(`cell-${cell.x}-${cell.y}`);
        oCell.classList.add('forReserveUnitDeploy');
        let finished = false
        this.onClick(oCell, () => this.takeAction('actReserveUnitsDeployement', { x: cell.x, y: cell.y, 
          finished: finished, 
          pId: playerid, 
          elem: elem,
          isWild: false,
          onStagingArea: false,
          unit_Id : unitId,
          misc_args : {}
        }));
      });
    },


    onLeavingStateReserveUnitsDeployement() {
      console.log('onLeavingStateReserveUnitsDeployement');
      //this.onEnteringStateReserveUnitsDeployement(args);
      this.clearPossible();
      this.resetPageTitle();
    },

    notif_clearEndReserveDeployement(n) {
      console.log('clearEndReserveDeployement');
      this.clearPossible();
      this.clearActionButtons();
      this.resetPageTitle();
    },

  



    /////////////////////////////////////////////////
    //  __  __          _ _            ____ _____
    // |  \/  | ___  __| (_) ___ ___  | __ )_   _|
    // | |\/| |/ _ \/ _` | |/ __/ __| |  _ \ | |
    // | |  | |  __/ (_| | | (__\__ \ | |_) || |
    // |_|  |_|\___|\__,_|_|\___|___/ |____/ |_|
    /////////////////////////////////////////////////
    onEnteringStateMedicsBTHeal(args) {
      this.makeUnitsSelectable(
        args.units,
        this.onClickUnitMedicsBTHeal.bind(this),
        this.isUnitSelectableMedicsBTHeal.bind(this),
        'activated',
        this.updateMedicsBTHealUnitsBtn.bind(this),
      );
      this.clearSelectedUnits();
      this.updateMedicsBTHealUnitsBtn();
    },

    isUnitSelectableMedicsBTHeal(unitId, pos, selected, minFilling) {
      return this.isUnitSelectableFinestHour(unitId, pos, selected, minFilling);
    },

    onClickUnitMedicsBTHeal(unitId, pos, selected) {
      if (!selected) return true;

      let nSelected = this._selectedUnits.reduce((carry, unit2Id) => carry + (unitId == unit2Id ? 1 : 0), 0);
      let maxN = this.getArgs().wounds[unitId];

      if (nSelected + 1 <= maxN && this.isUnitSelectableFinestHour(unitId, null, false, null)) {
        this._selectedUnits.push(unitId);
        $(`unit-${unitId}`).dataset.selected = nSelected + 1;
      } else {
        this._selectedUnits = this._selectedUnits.filter((unit2Id) => unitId != unit2Id);
        $(`unit-${unitId}`).classList.remove('activated');
        $(`unit-${unitId}`).dataset.selected = 0;
      }

      return false;
    },

    updateMedicsBTHealUnitsBtn() {
      dojo.destroy('btnConfirmHeal');
      if (this._selectedUnits.length > 0) {
        this.addPrimaryActionButton('btnConfirmHeal', _('Confirm healing(s)'), () => this.onClickConfirmMedicsBTHeal());
      } else {
        this.addDangerActionButton('btnConfirmHeal', _('Confirm no healing and end your turn'), () =>
          this.onClickConfirmMedicsBTHeal(),
        );
      }
    },

    onClickConfirmMedicsBTHeal() {
      this.takeAction('actMedicsBTHeal', {
        unitIds: this._selectedUnits.join(';'),
      });
    },

    ////////////////////////////////////////////////////////////
    // ______ _                ______      _     _            
    // | ___ \ |               | ___ \    (_)   | |           
    // | |_/ / | _____      __ | |_/ /_ __ _  __| | __ _  ___ 
    // | ___ \ |/ _ \ \ /\ / / | ___ \ '__| |/ _` |/ _` |/ _ \
    // | |_/ / | (_) \ V  V /  | |_/ / |  | | (_| | (_| |  __/
    // \____/|_|\___/ \_/\_/   \____/|_|  |_|\__,_|\__, |\___|
    //                                              __/ |     
    //                                           |___/                 
    ////////////////////////////////////////////////////////////

    onEnteringStateTargetBridge(args) {
      //$('terrains-' + args.terrainId).classList.add('selectable');
      console.log('Entering state TargetBlowbridge JD', args.terrains);
      this._selectedTerrains = [];
      this._selectableTerrains = args.terrains;
      
      Object.keys(this._selectableTerrains).forEach((tId) => {
        const terrainId = this._selectableTerrains[tId]['id'];
        let x = this._selectableTerrains[tId]['x'];
        let y = this._selectableTerrains[tId]['y'];
        // obtenir la cell qui contient le terrain
        let oCell = $(`cell-${x}-${y}`);
        this.onClick(oCell, () => {
          dojo.destroy('btnRestartTurn');
          this._selectedTerrains.push(this._selectableTerrains[tId]);
          this.takeAction('actBlowBridge', { terrainsIds: this._selectedTerrains[0]['id']});
        })
      })

      this.addDangerActionButton('btnRestartTurn', _('Undo actions'), () => {
          this.takeAction('actRestart');
        },
        'restartAction',
      );
    },


    onEnteringStateBlowBridge(args) {
      console.log('Entering Blow Bridge Action JD ');
    },


    onClickTargetBridge(terrainId, pos, selected) {
      console.log('On ClickBlowbridge JD selected terrains ', this._selectedTerrains, terrainId);
      // Add a number on the bridge
      if (!selected) {
        $(`terrain-${terrainId}`).dataset.blowBridgeOrder = this._selectedTerrains.length + 1;
      }

      dojo.destroy('btnClearSelectedUnits');
      if (this._selectedTerrains.length - (selected ? 1 : 0) > 0) {
        this.addSecondaryActionButton('btnClearSelectedUnits', _('Clear'), () => this.clearSelectedTerrains());
      }
      return true;
    },

    isSelectableTargetBridge(terrainId, pos, selected, minFilling) {
      console.log('Is selected terrains JD', this._selectedTerrains);
      // If no selected terrain yet, can select any terrain
      if (this._selectedTerrains.length == 0) return true;
      let lastTerrainId = this._selectedTerrains[this._selectedTerrains.length - 1];
      // A selected unit can only be unselected if it's the last one
      if (selected) return terrainId == lastTerrainId;
      // No more than 1 bridge
      if (this._selectedTerrains.length == 1) return false;
      // Otherwise, it must be adjacent to the last selected unit
      //let lastTerrainPos = this.getArgs()['terrains'][lastTerrainId];
      //return Math.abs(pos.x - lastTerrainPos.x) + 2 * Math.abs(pos.y - lastTerrainPos.y) <= 3;
    },




  });
});
