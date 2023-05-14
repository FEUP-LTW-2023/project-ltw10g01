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
        print_r($dictionaryInquiriesResponses);
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
            <?php echo $ticket->getDescription() ?>
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
                    <button id="AcceptTicketFromInquiry" type="submit" name="idTicket" value="<?php echo $ticket->getIdTicket() ?>">
                        Accept
                    </button>
                </form>
                <form method="post" action="../actions/action_deleteInquiry.php">
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
            <div>
    <?php if($ticket->getCria()===$inquiry->getUserGiving()){ ?>
            <?php echo $notificationNumber ?> New Message(s) From Client <?php echo User::getSingleUser($db,$inquiry->getIdInquiry())->getName() ?>
    <?php }
        else if ($ticket->getResolve()===$inquiry->getIdInquiry()){ ?>
            <?php echo $notificationNumber ?> New Message(s) From Agent <?php echo User::getSingleUser($db,$inquiry->getIdInquiry())->getName() ?>
        <?php }
        else echo $ticket->getResolve(); echo $ticket->getCria(); echo $inquiry->getUserGiving() ; echo $inquiry->getUserReceiving()?>
            </div>
            <h2 class="ticketText"><?php echo $ticket->getTitle()?></h2>
        </section>
        <section>
            <h3 class="ticketDescription"><?php echo $ticket->getDescription()?></h3>
        </section>
        <section>
            <h5>
                Last Message: <?php echo $ticket->getLastReplyFromTicket($db,$inquiry->getUserGiving()) ?>
            </h5>
            <form method="post" action="../actions/action_deleteInquiry.php">
                <button type="submit" name="Inquiry" value="<?php echo $inquiry->getIdInquiry() ?>"
            </form>
        </section>
    </section>
<?php }