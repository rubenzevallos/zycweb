<?php # vim:ts=2:sw=2:et:
/* For licensing and copyright terms, see the file named LICENSE */

class MTrackWatch {
  static $possible_event_types = array();
  static $media = array(
    'email' => 'Email',
//    'timline' => 'Timeline'
  );

  static function registerEventTypes($objecttype, $events) {
    self::$possible_event_types[$objecttype] = $events;
  }

  static function getWatchUI($object, $id) {
    ob_start();
    self::renderWatchUI($object, $id);
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
  }

  static function renderWatchUI($object, $id) {
    $me = mtrack_canon_username(MTrackAuth::whoami());
    if ($me == 'anonymous' || MTrackAuth::getUserClass() == 'anonymous') {
      return;
    }

    global $ABSWEB;
    $url = $ABSWEB . 'admin/watch.php?' .
      http_build_query(array('o' => $object, 'i' => $id));
    $evts = json_encode(self::$possible_event_types[$object]);
    $media = json_encode(self::$media);
    $val = new stdclass;
    foreach (MTrackDB::q('select medium, event from watches where otype = ? and oid = ? and userid = ? and active = 1', $object, $id, $me)->fetchAll() as $row)
    {
      $val->{$row['medium']}->{$row['event']} = true;
    }
    $val = json_encode($val);
    echo <<<HTML
 <button id='watcher-$object-$id' type='button'>Watch</button>
<script>
$(document).ready(function () {
  var evts = $evts;
  var media = $media;
  var V = $val;
  $('#watcher-$object-$id').click(function () {
    var dlg = $('<div title="Watching"/>');
    var frm = $('<form/>');
    var tbl = $('<table/>');
    var tr = $('<tr/>');
    tr.append('<th>Event</th>');
    for (var m in media) {
      var th = $('<th/>');
      th.text(media[m]);
      tr.append(th);
    }
    tbl.append(tr);

    for (var i in evts) {
      tr = $('<tr/>');
      var td = $('<td/>');
      td.text(evts[i]);
      tr.append(td);

      for (var m in media) {
        td = $('<td/>');
        var cb = $('<input type="checkbox"/>');
        if (V[m] && V[m][i]) {
          cb.attr('checked', 'checked');
        }
        cb.data('medium', m);
        cb.data('event', i);
        td.append(cb);
        tr.append(td);
      }
      tbl.append(tr);
    }
    frm.append(tbl);
    dlg.append(frm);
    dlg.dialog({
      autoOpen: true,
      bgiframe: true,
      resizable: false,
      width: 600,
      modal: true,
      buttons: {
        'Ok': function() {
          V = {};
          $("input[type='checkbox'][checked]", dlg).each(function () {
            var m = $(this).data('medium');
            var e = $(this).data('event');
            if (!V[m]) {
              V[m] = {};
            }
            V[m][e] = true;
          });
          $.post('$url', {w: JSON.stringify(V)});
          $(this).dialog('close');
          dlg.remove();
        }
      }
    });
  });
});
</script>
HTML;
  }

  /* Returns an array, keyed by watching entity, of objects that changed
   * since the specified date.
   * $watcher = null means all watchers, otherwise specifies the only watcher of interest.
   * $medium specifies timeline or email (or some other medium)
   */
  static function getWatchedItemsAndWatchers($since, $medium, $watcher = null) {
    if ($watcher) {
      $q = MTrackDB::q('select otype, oid, userid, event from watches where active = 1 and medium = ? and userid = ?', $medium, $watcher);
    } else {
      $q = MTrackDB::q('select otype, oid, userid, event from watches where active = 1 and medium = ?', $medium);
    }
    $watches = $q->fetchAll(PDO::FETCH_ASSOC);

    $last = strtotime($since);
    $LATEST = $last;

    $db = MTrackDB::get();
    $changes = MTrackDB::q(
      "select * from changes where changedate > ? order by changedate asc",
      MTrackDB::unixtime($last))->fetchAll(PDO::FETCH_OBJ);
    $cids = array();
    $cs_by_cid = array();
    $by_object = array();
    foreach ($changes as $CS) {
      $cids[] = $CS->cid;
      $cs_by_cid[$CS->cid] = $CS;
      $t = strtotime($CS->changedate);
      if ($t > $LATEST) {
        $LATEST = $t;
      }

      list($object, $id) = explode(':', $CS->object, 3);
      $by_object[$object][$id][] = $CS->cid;
    }

    $repo_by_id = array();
    $changesets_by_repo_and_rev = array();
    $related_projects = array();

    foreach (MTrackDB::q('select repoid from repos')
        ->fetchAll(PDO::FETCH_COLUMN, 0) as $repoid) {
      $repo = MTrackRepo::loadById($repoid);
      $repo_by_id[$repoid] = $repo;

      foreach ($repo->history(null, MTrackDB::unixtime($last)) as $e) {
        /* SCM doesn't always respect our date range */
        $t = strtotime($e->ctime);
        if ($t <= $last) {
          continue;
        }
        if ($t > $LATEST) {
          $LATEST = $t;
        }

        $key = $repo->getBrowseRootName() . ',' . $e->rev;
        $e->repo = $repo;
        $changesets_by_repo_and_rev[$key] = $e;

        $e->_related = array();

        /* relationships to projects based on path */
        $projid = $repo->projectFromPath($e->files);
        if ($projid !== null) {
          $e->_related[] = array('project', $projid);
          $related_projects[$projid] = $projid;
        }
      }
    }

    /* Ensure that changesets are sorted chronologically */
    uasort($changesets_by_repo_and_rev, array('MTrackWatch', '_compare_cs'));

    /* Look at the changed tickets: match the reason back to one of the
     * above changesets */
    if (isset($by_object['ticket'])) {
      foreach ($by_object['ticket'] as $tid => $cslist) {
        foreach ($cslist as $cid) {
          $CS = $cs_by_cid[$cid];
          if (!preg_match_all(
                "/\(In \[changeset:(([^,]+),([a-zA-Z0-9]+))\]\)/sm",
                $CS->reason, $CSM)) {
            continue;
          }
          // $CSM[2] => repo
          // $CSM[3] => changeset
          foreach ($CSM[2] as $csm => $csm_repo) {
            $csm_rev = $CSM[3][$csm];

            /* Look for the repo changeset */
            $key = "$csm_repo,$csm_rev";
            if (isset($changesets_by_repo_and_rev[$key])) {
              $e = $changesets_by_repo_and_rev[$key];
              $e->CS = $CS;
              $CS->ent = $e;
            }
          }
        }
      }
    }

    $tkt_list = array();
    $proj_by_tid = array();
    $emails_by_tid = array();
    $emails_by_pid = array();
    $owners_by_csid = array();
    $milestones_by_tid = array();
    $milestones_by_cid = array();

    /* determine linked projects and group emails */
    if (count($related_projects)) {
      $projlist = join(',', $related_projects);
      foreach (MTrackDB::q(
          "select projid, notifyemail from projects where
          notifyemail is not null and projid in ($projlist)")
          ->fetchAll(PDO::FETCH_NUM) as $row) {
        $emails_by_pid[$row[0]] = $row[1];
      }
    }

    if (isset($by_object['ticket'])) {
      $tkt_owner_ids = array();
      $tkt_cid_list = array();
      $tkt_milestone_fields = array();

      foreach ($by_object['ticket'] as $tid => $cidlist) {
        $tkt_list[] = $db->quote($tid);
        $tkt_owner_ids[] = $db->quote("ticket:$tid:owner");
        foreach ($cidlist as $cid) {
          $tkt_cid_list[$cid] = $cid;
        }
        /* also want to include folks that were Cc'd */
        $tkt_owner_ids[] = $db->quote("ticket:$tid:cc");
        /* milestones */
        $tkt_milestone_fields[] = $db->quote("ticket:$tid:@milestones");
      }
      $tkt_list = join(',', $tkt_list);

      foreach (MTrackDB::q(
          "select t.tid, p.projid, notifyemail from tickets t left join ticket_components tc on t.tid = tc.tid left join components_by_project cbp on cbp.compid = tc.compid left join projects p on cbp.projid = p.projid where p.projid is not null and t.tid in ($tkt_list)")->fetchAll(PDO::FETCH_NUM) as $row) {
        $proj_by_tid[$row[0]][$row[1]] = $row[1];
        if (isset($row[2]) && strlen($row[2])) {
          $emails_by_tid[$row[0]] = $row[2];
          $emails_by_pid[$row[1]] = $row[2];
        }
      }

      /* determine all changed owners in the affected period */
      $tkt_owner_ids = join(',', $tkt_owner_ids);
      $tkt_cid_list = join(',', $tkt_cid_list);
      foreach (MTrackDB::q(
          "select cid, oldvalue, value from change_audit where cid in ($tkt_cid_list) and fieldname in ($tkt_owner_ids)")->fetchAll(PDO::FETCH_NUM) as $row) {
        $cid = array_shift($row);
        foreach ($row as $owner) {
          if (!strlen($owner)) continue;
          $owners_by_csid[$cid][$owner] = mtrack_canon_username($owner);
        }
      }

      /* determine all changed milestones in the affected period */
      $tkt_milestone_fields = join(',', $tkt_milestone_fields);
      foreach (MTrackDB::q(
          "select cid, oldvalue, value from change_audit where cid in ($tkt_cid_list) and fieldname in ($tkt_milestone_fields)")->fetchAll(PDO::FETCH_NUM) as $row) {
        $cid = array_shift($row);
        foreach ($row as $ms) {
          $ms = split(',', $ms);
          foreach ($ms as $mid) {
            $mid = (int)$mid;
            $milestones_by_cid[$cid][$mid] = $mid;
          }
        }
      }

      foreach (MTrackDB::q(
          "select tid, mid from ticket_milestones where tid in ($tkt_list)")
          ->fetchAll(PDO::FETCH_NUM) as $row) {
        $milestones_by_tid[$row[0]][$row[1]] = $row[1];
      }
    }

    /* walk through list of objects and add related objects */
    if (isset($by_object['ticket'])) {
      foreach ($by_object['ticket'] as $tid => $cslist) {
        foreach ($cslist as $cid) {
          $CS = $cs_by_cid[$cid];
          if (!isset($CS->_related)) {
            $CS->_related = array();
          }

          if (isset($CS->ent)) {
            $CS->_related[] = array('repo', $CS->ent->repo->repoid);
          }
          if (isset($proj_by_tid[$tid])) {
            foreach ($proj_by_tid[$tid] as $pid) {
              $CS->_related[] = array('project', $pid);
            }
          }
          if (isset($milestones_by_tid[$tid])) {
            foreach ($milestones_by_tid[$tid] as $mid) {
              $CS->_related[] = array('milestone', $mid);
            }
          }
          if (isset($milestones_by_cid[$cid])) {
            foreach ($milestones_by_cid[$cid] as $mid) {
              $CS->_related[] = array('milestone', $mid);
            }
          }
        }
      }
    }
    foreach ($changesets_by_repo_and_rev as $ent) {
      $ent->_related[] = array('repo', $ent->repo->repoid);
    }

    /* having determined all changed items, make a pass through to determine
     * how to associate those with watchers.
     * Watchers are one of:
     * - an user with a matching watches entry
     * - the group email address associated with a project associated with the
     *   changed object
     * - the owner of a ticket
     */

    /* generate synthetic watcher entries for project group emails */
    foreach ($emails_by_pid as $pid => $email) {
      $watches[] = array(
        'otype' => 'project',
        'oid' => $pid,
        'userid' => $email,
        'event' => '*',
      );
    }

    foreach ($by_object as $otype => $objects) {
      foreach ($objects as $oid => $cidlist) {
        foreach ($cidlist as $cid) {
          $CS = $cs_by_cid[$cid];
          if (isset($owners_by_csid[$cid])) {
            /* add synthetic watcher for a past or current owner */
            foreach ($owners_by_csid[$cid] as $owner) {
              $watches[] = array(
                'otype' => $otype,
                'oid' => $oid,
                'userid' => $owner,
                'event' => '*'
              );
            }
          }
          self::_compute_watch($watches, $otype, $oid, $CS);
          /* eliminate from the set if there are no watchers */
          if (!isset($CS->_watcher)) {
            unset($cs_by_cid[$cid]);
          }
        }
      }
    }
    foreach ($changesets_by_repo_and_rev as $key => $ent) {
      self::_compute_watch($watches, 'changeset', $key, $ent);
      /* eliminate from the set if there are no watchers */
      if (!isset($ent->_watcher)) {
        unset($changesets_by_repo_and_rev[$key]);
      }
    }

    /* now collect the data by watcher */
    $by_watcher = array();
    foreach ($cs_by_cid as $CS) {
      foreach ($CS->_watcher as $user) {
        $by_watcher[$user][$CS->cid] = $CS;
      }
    }
    foreach ($changesets_by_repo_and_rev as $key => $ent) {
      foreach ($ent->_watcher as $user) {
        /* don't add this if we have an associated CS */
        if (isset($ent->CS) && $by_watcher[$user][$ent->CS->cid]) {
          continue;
        }
        $by_watcher[$user][$key] = $ent;
      }
    }
    /* one last pass to group the data by object */
    foreach ($by_watcher as $user => $items) {
      foreach ($items as $key => $obj) {
        if ($obj instanceof MTrackSCMEvent) {
          /* group by repo */
          $nkey = "repo:" . $obj->repo->repoid;
        } else {
          $nkey = $obj->object;
        }
        unset($by_watcher[$user][$key]);
        $by_watcher[$user][$nkey][] = $obj;
      }
    }

    return $by_watcher;
  }

  static function _compute_watch($watches, $otype, $oid, $obj, $event = null) {
    foreach ($watches as $row) {
      if ($row['otype'] != $otype) continue;
      if ($row['oid'] != '*' && $row['oid'] != $oid) continue;
      if ($event === null || $row['event'] == '*' || $row['event'] == $event) {
        if (!isset($obj->_watcher)) {
          $obj->_watcher = array();
        }
        $obj->_watcher[$row['userid']] = $row['userid'];
      }
    }
    if ($event === null && isset($obj->_related)) {
      foreach ($obj->_related as $rel) {
        self::_compute_watch($watches, $rel[0], $rel[1], $obj, $otype);
      }
    }
  }

  static function _get_project($pid) {
    static $projects = array();
    if (isset($projects[$pid])) {
      return $projects[$pid];
    }
    $projects[$pid] = MTrackProject::loadById($pid);
    return $projects[$pid];
  }

  /* comparison function for MTrackSCMEvent objects that sorts in ascending
   * chronological order */
  static function _compare_cs($A, $B) {
    return strcmp($A->ctime, $B->ctime);
  }
}

