<?php
/*
 *  $Id: NotCondition.php 905 2010-10-05 16:28:03Z mrook $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://phing.info>.
 */

require_once 'phing/tasks/system/condition/ConditionBase.php';

/**
 *  <not> condition.
 *
 *  Evaluates to true if the single condition nested into it is false
 *  and vice versa.
 *
 *  @author    Andreas Aderhold <andi@binarycloud.com>
 *  @copyright © 2001,2002 THYRELL. All rights reserved
 *  @version   $Revision: 905 $ $Date: 2010-10-05 18:28:03 +0200 (Tue, 05 Oct 2010) $
 *  @access    public
 *  @package   phing.tasks.system.condition
 */
class NotCondition extends ConditionBase implements Condition {

    function evaluate() {
        if ($this->countConditions() > 1) {
            throw new BuildException("You must not nest more than one condition into <not>");
        }
        if ($this->countConditions() < 1) {
            throw new BuildException("You must nest a condition into <not>");
        }
        $conds = $this->getIterator();
        return !$conds->current()->evaluate();
    }
}
