<?php

function drawUserInquiries(PDO $db, array $inquiries){ ?>
    <main id="Inquiries">
        <?php
        $dictionaryInquiriesResponses = array();
        foreach ($inquiries as $inquiry){
            if ($inquiry->getType()==="TICKET_RESPONDED"){
                if(array_key_exists($inquiry->getTicket(),$dictionaryInquiriesResponses)){
                    $dictionaryInquiriesResponses[$inquiry->getTicket()] +=1;
                }
                else{
                    $dictionaryInquiriesResponses[$inquiry->getTicket()] = 1;
                }
            }
        }
        foreach ($inquiries as $inquiry){
            $inquiryType = $inquiry->getType();
            if($inquiryType==="ASSIGN_AGENT"){
                drawTicketAssignRequest($db,$inquiry);
            }
            else if($inquiryType==="TICKET_RESPONDED"){
                if($inquiry->getIdInquiry()===Inquiry::getLastInquiryFromTicket($db,$inquiry->getTicket())) {
                    drawTicketResponded($db, $inquiry, $dictionaryInquiriesResponses[$inquiry->getTicket()]);
                }
            }
            else if($inquiryType==="CHANGE_STATUS"){
                drawTicketChangeStatus($db,$inquiry);
            }
        }
        ?>
    </main>
<?php }

function drawTicketAssignRequest(PDO $db, Inquiry $inquiry){
    $userRequesting = User::getSingleUser($db,$inquiry->getUserGiving());
    $ticket=Ticket::getTicketFromId($db,$inquiry->getTicket())?>
    <section class="retangulo">
        <h5> Requested by: <?php echo $userRequesting->getName() ?></h5>
        <h2 class="ticketText"> <?php echo $ticket->getTitle() ?></h2>
        <section class="ticketDescription">
            <h3>
                <?php echo $ticket->getDescription() ?>
            </h3>
        </section>
        <section>
            <article>
                <h5>Departament: <?=$ticket->getTicketDepartmentName($db)?></h5>
                <h5>Status: <?=$ticket->getLastTicketStatus($db)?></h5>
                <h5>Date: <?=$ticket->getCreateDate()?></h5>
            </article>
            <article class="AssignTicket">
                <form method="post" action="../actions/action_assign_to_agent.php">
                    <input type="hidden" name="Inquiry" value="<?php echo $inquiry->getIdInquiry() ?>">
                    <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                    <button id="AcceptTicketFromInquiry" type="submit" name="idTicket" value="<?php echo $ticket->getIdTicket() ?>">
                        Accept
                    </button>
                </form>
                <form method="post" action="../actions/action_deleteInquiryAssignAgent.php">
                    <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                    <button id="RejectTicketFromInquiry" type="submit" name="idInquiry" value="<?php echo $inquiry->getIdInquiry() ?>">
                        Reject
                    </button>
                </form>
            </article>
        </section>
    </section>
<?php }

function drawTicketResponded(PDO $db, Inquiry $inquiry, int $notificationNumber){
    $ticket = Ticket::getTicketFromId($db,$inquiry->getTicket());
    ?>
    <section class="retangulo">
        <section class = "AssignTicket">
            <div id="InformationAssignTicket">
    <?php if($ticket->getCria()===$inquiry->getUserGiving()){ ?>
            <?php echo $notificationNumber ?> New Message(s) From Client <?php echo User::getSingleUser($db,$inquiry->getUserGiving())->getName() ?>
    <?php }
        else if ($ticket->getResolve()===$inquiry->getUserGiving()){ ?>
            <?php echo $notificationNumber ?> New Message(s) From Agent <?php echo User::getSingleUser($db,$inquiry->getUserGiving())->getName() ?>
        <?php } ?>
            </div>
            <h2 class="ticketText"><?php echo $ticket->getTitle()?></h2>
        </section>
        <section>
            <h3 class="ticketDescription"><?php echo $ticket->getDescription()?></h3>
        </section>
        <section>
            <h5>
                <?php
                $lastReply = $ticket->getLastReplyFromTicket($db,$inquiry->getUserGiving());
                $ExibingText = strlen($lastReply) > 58 ? substr($lastReply, 0, 58) . '...' : $lastReply; ?>
                Last Message: <?php echo $ExibingText ?>
            </h5>
            <form method="post" action="../actions/action_deleteInquiriesTicketResponded.php">
                <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                <button type="submit" name="Ticket" value="<?php echo $ticket->getIdTicket() ?>"> See more </button>
            </form>
        </section>
    </section>
<?php }


function drawTicketChangeStatus(PDO $db,Inquiry $inquiry){
    $ticket = Ticket::getTicketFromId($db,$inquiry->getTicket());
    $agent = $ticket->getResolve();
    $agentName="";
    if($agent){
        $agentName = User::getSingleUser($db,$agent)->getName();
    }
    $status = $ticket->getLastTicketStatus($db); ?>
    <section class="retangulo">
        <h4 id="ThisTicket"> This Ticket that you created:</h4>
        <article id="TicketDescription">
            <h2 class="ticketText"><?php echo $ticket->getTitle()?></h2>
            <section>
                <h3 class="ticketDescription"><?php echo $ticket->getDescription()?></h3>
            </section>
        </article>
        <section>
            <h4>
                Now has the status <?php echo $status; if ($status!="OPEN") echo ", by agent " . $agentName?>
            </h4>
            <form method="post" action="../actions/action_deleteInquiryAssignAgent.php">
                <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                <button type="submit" name="idInquiry" value="<?php echo $inquiry->getIdInquiry() ?>"> Got It </button>
            </form>
        </section>
    </section>

<?php }