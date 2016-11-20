<?php
/**
 * An example plugin.
 *
 * @copyright 2008-2014 Vanilla Forums, Inc.
 * @license GNU GPLv2
 */

// Define the plugin:
$PluginInfo['character'] = array(
    'Description' => 'Character management for storygaming in in Vanilla.',
    'Version' => '0.1',
    'RequiredApplications' => array('Vanilla' => '2.1'),
    'RequiredTheme' => false,
    'RequiredPlugins' => false,
    'HasLocale' => false,
    'License' => 'GNU GPL2',
    'SettingsUrl' => '/plugin/character',
    'SettingsPermission' => 'Garden.Settings.Manage',
    'Author' => "Adam Robichaud",
    'AuthorUrl' => 'http://apo.calypti.ca',
    'MobileFriendly'=>TRUE,
);

function GET($key, $default = "")
{
  return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
}

function POST($key, $default = "")
{
  return array_key_exists($key, $_POST) ? $_GET[$key] : $default;
}

/**
 * Class CharacterPlugin
 *
 * @see http://docs.vanillaforums.com/developers/plugins
 * @see http://docs.vanillaforums.com/developers/plugins/quickstart
 */
class CharacterPlugin extends Gdn_Plugin {

    /**
     * Plugin constructor
     *
     * This fires once per page load, during execution of bootstrap.php. It is a decent place to perform
     * one-time-per-page setup of the plugin object. Be careful not to put anything too strenuous in here
     * as it runs every page load and could slow down your forum.
     */
    public function __construct() {
        // LogMessage(__FILE__,__LINE__,'CharacterPlugin','__construct',"Constructing CharacterPlugin");
    }

    /**
     * Create a method called "Example" on the PluginController
     *
     * One of the most powerful tools at a plugin developer's fingertips is the ability to freely create
     * methods on other controllers, effectively extending their capabilities. This method creates the
     * Example() method on the PluginController, effectively allowing the plugin to be invoked via the
     * URL: http://www.yourforum.com/plugin/Example/
     *
     * From here, we can do whatever we like, including turning this plugin into a mini controller and
     * allowing us an easy way of creating a dashboard settings screen.
     *
     * @param $Sender Sending controller instance
     */
    public function pluginController_character_create($Sender) {
        /*
         * If you build your views properly, this will be used as the <title> for your page, and for the header
         * in the dashboard. Something like this works well: <h1><?php echo T($this->Data['Title']); ?></h1>
         */
        $Sender->title('Character Plugin');
        $Sender->addSideMenu('plugin/character');

        // If your sub-pages use forms, this is a good place to get it ready
        $Sender->Form = new Gdn_Form();

        /*
         * This method does a lot of work. It allows a single method (PluginController::Example() in this case)
         * to "forward" calls to internal methods on this plugin based on the URL's first parameter following the
         * real method name, in effect mimicing the functionality of as a real top level controller.
         *
         * For example, if we accessed the URL: http://www.yourforum.com/plugin/Example/test, Dispatch() here would
         * look for a method called ExamplePlugin::Controller_Test(), and invoke it. Similarly, we we accessed the
         * URL: http://www.yourforum.com/plugin/Example/foobar, Dispatch() would find and call
         * ExamplePlugin::Controller_Foobar().
         *
         * The main benefit of this style of extending functionality is that all of a plugin's external API is
         * consolidated under one namespace, reducing the chance for random method name conflicts with other
         * plugins.
         *
         * Note: When the URL is accessed without parameters, Controller_Index() is called. This is a good place
         * for a dashboard settings screen.
         */
        $this->dispatch($Sender, $Sender->RequestArgs);
    }

    /**
     * Always document every method.
     *
     * @param $Sender
     */
    public function controller_index($Sender) {
        // Prevent non-admins from accessing this page
        $Sender->permission('Garden.Settings.Manage');
        $Sender->setData('PluginDescription',$this->getPluginKey('Description'));

        /*
	      $Validation = new Gdn_Validation();
        $ConfigurationModel = new Gdn_ConfigurationModel($Validation);
        $ConfigurationModel->setField(array(
            'Plugin.Character.RenderCondition'     => 'all',
            'Plugin.Character.TrimSize'      => 100
        ));

        // Set the model on the form.
        $Sender->Form->setModel($ConfigurationModel);

        // If seeing the form for the first time...
        if ($Sender->Form->authenticatedPostBack() === false)
        {
            // Apply the config settings to the form.
            $Sender->Form->setData($ConfigurationModel->Data);
        }
        else
        {
            $ConfigurationModel->Validation->applyRule('Plugin.Character.RenderCondition', 'Required');
            $ConfigurationModel->Validation->applyRule('Plugin.Character.TrimSize', 'Required');
            $ConfigurationModel->Validation->applyRule('Plugin.Character.TrimSize', 'Integer');
            $Saved = $Sender->Form->save();
            if ($Saved) {
                $Sender->StatusMessage = t("Your changes have been saved.");
            }
        }
        */

        // GetView() looks for files inside plugins/PluginFolderName/views/ and returns their full path. Useful!
        $Sender->render($this->getView('character.php'));
    }

    public function DiscussionController_BeforeBodyField_Handler($Sender)
    {
      $user = Gdn::session()->User->UserID;
      $characters = $this->GetCharacters(["Game" => $Sender->Discussion->Category, "Owner" => $user]);
      $sheets = array();
      foreach ( $characters as $character )
      {
        $sheets[$character['Slug']] = $character['Sheet'];
      }
      if (count($characters) > 0)
      {
        ?>
        <select id='character-select'>
          <?php foreach ($characters as $character) : ?>
            <option value='<?php echo $character['Slug']; ?>'><?php echo $character['Name']; ?></option>
          <?php endforeach; ?>
        </select>
        <a class='button character-dialog' href='#'><i class="fa fa-comment-o" aria-hidden="true"></i> Say</a>
        <a id='character-sheet-link' class='button' href='<?php echo $characters[0]['Sheet']; ?>' target="_blank" data-sheets='<?php echo json_encode($sheets); ?>'><i class="fa fa-file-text-o" aria-hidden="true"></i> Sheet</a>
        <a class='button' href='<?php echo "/discussions/characters/new/" . $Sender->Discussion->Category; ?>'><i class="fa fa-plus" aria-hidden="true"></i> New</a>
        <?php
      }
      else
      {
        ?><span>You don't have any characters set up. Please go <a href='/discussions/characters/new/<?php echo $Sender->Discussion->Category?>'>here</a> to do that!</span><?php
      }
    }

    public function Base_Render_Before($Sender) {
      // Add "Mark All Viewed" to main menu
      $Sender->addCssFile('character.css', 'plugins/character');
      $Sender->addJsFile('character.js', 'plugins/character');

      if ($Sender->Menu && Gdn::Session()->IsValid()) {
        $Sender->Menu->AddLink('Characters', T('Characters'), '/discussions/characters');
      }
    }

    public function RootController_Api_Create($Sender, $Args)
    {
      $data = [];
      if ( array_key_exists(0, $Args) )
      {
        switch ( $Args[0] )
        {
          case 'characters':
            $id = (array_key_exists(1, $Args) ? $Args[1] : false);
            if ($id) $data = (array)$this->GetCharacter($id);
            else $data = (array)$this->GetCharacters();
            break;
          default:
            break;
        }
      }
      $Sender->SetData($data);
      $Sender->RenderData();
    }

    public function GetCharacters($where = [])
    {
      $Validation = new Gdn_Validation();
      $CModel = new Gdn_Model('Characters', $Validation);
      $Query = $CModel->GetWhere($where);
      $results = $Query->resultArray();
      return $results;
    }

    public function GetCharacter($id)
    {
      $character = false;
      $Validation = new Gdn_Validation();
      $CModel = new Gdn_Model('Characters', $Validation);
      $Query = $CModel->GetWhere(array('CharacterId' => $id));
      if ( $Query->count() == 1 )
      {
        $character = $Query->FirstRow('', DATASET_TYPE_ARRAY);
      }

      if ( $character === false )
      {
        // Fall back on slug
        $Query = $CModel->GetWhere(array('Slug' => $id));
        if ( $Query->count() == 1 )
        {
          // Error out: No unique character with provided ID or Slug found
          $character = $Query->FirstRow('', DATASET_TYPE_ARRAY);
        }
      }
      return $character;
    }

    public function PostCharacter($Sender, $data)
    {
      $Validation = new Gdn_Validation();
      $CModel = new Gdn_Model('Characters', $Validation);
      foreach ( $data as $key => $value )
      {
        $character[$key] = $value;
      }
      unset($character["CharacterId"]);
      $Sender->Form->SetData($character);
      $Sender->Form->SetFormValue('Owner', Gdn::session()->UserID);
      $Sender->Form->SetModel($CModel);
      $return = $Sender->Form->Save($character);
      return $return;
    }

    public function PutCharacter($Sender, $data)
    {
      // TODO: Doesn't work. Fuck me.
      $Validation = new Gdn_Validation();
      $CModel = new Gdn_Model('Characters', $Validation);

      foreach ( $data as $key => $value )
      {
        $character[$key] = $value;
      }

      $Sender->Form->SetData($character);
      $Sender->Form->SetModel($CModel);
      return $Sender->Form->Save();
    }

    public function DeleteCharacter($id)
    {
      $Validation = new Gdn_Validation();
      $CModel = new Gdn_Model('Characters', $Validation);
      return $CModel->Delete([ "CharacterId" => $id ]);
    }

    public function DiscussionsController_Characters_Create($Sender, $args)
    {
      $cmd = array_key_exists(0, $args) ? $args[0] : '';
      switch ( $cmd )
      {
        case 'delete':

          if ( !array_key_exists(1, $args) )
          {
            // Error out: No ID supplied. How the fuck did we get here?
            $Sender->SetData("error", "No ID Supplied");
            $Sender->renderData();
            break;
          }
          $character = $this->GetCharacter($args[1]);
          $resp = false;
          if ($character->Owner != Gdn::session()->UserID && Gdn::session()->User->Admin != "1")
          {
            $resp = "You don't have permission to edit that character.";
          }
          else
          {
            $resp = $this->DeleteCharacter($args[1]);
            $Sender->InformMessage(sprintf(T('Character Deleted at saved at %s'), Gdn_Format::Date()));
          }

          $Sender->SetData("error", $resp);
          $Sender->renderData();
          break;

        case '':
          $characters = $this->GetCharacters();
          $Sender->SetData('characters', $characters);
          $Sender->render($this->getView('characters.php'));
          break;
        case 'edit':
          // Book keeping
          $Sender->SetData("Error", "");

          if ( !array_key_exists(1, $args) )
          {
            // Error out: No ID supplied. How the fuck did we get here?
            $Sender->SetData("Error", "No ID Supplied");
            $Sender->render($this->getView('character-new.php'));
            break;
          }
          else
          {
            $character = $this->GetCharacter($args[1]);
            if ($character === false)
            {
              $Sender->SetData("Error", "Invalid ID Supplied");
              $Sender->render($this->getView('character-new.php'));
              break;
            }

            if ($character->Owner != Gdn::session()->UserID && Gdn::session()->User->Admin != "1")
            {
              $Sender->SetData("Error", "You don't have permission to edit that &ndash; you saucy macaroni, you.");
              $Sender->render($this->getView('character-new.php'));
              break;
            }

            $id = false;
            if ( array_key_exists('Slug', $_POST) )
            {
              $id = $this->PutCharacter($Sender, $_POST);
              if ( $character->CharacterId != $id )
              {
                $Sender->SetData("Error", "I dunno what the hell happened, but you just edited a character that wasn't who you asked to edit. I'm sorry. This is uncharted territory for me, and maybe it's a little bit Vanilla's fault for being such a terrible platform to develop for.<br><br>Seriously guys, fix your shit!");
                $Sender->render($this->getView('character-new.php'));
                break;
              }
              $character = $this->GetCharacter($id);
              $Sender->SetData("success", sprintf(T('Character saved at %s.'), Gdn_Format::Date()));
            }
            $Sender->SetData('character', $character);
          }
          $Sender->render($this->getView('character-new.php'));
          break;

        case 'new':
          $character = false;

          if ( array_key_exists(1, $args) )
          {
            $character->Game = $args[1];
          }

          if ( array_key_exists('Slug', $_POST) )
          {
            $id = $this->PostCharacter($Sender, $_POST);
            if ( $id === false )
            {
              $post = json_encode($_POST);
              $Sender->InformMessage(sprintf(T('Character could not be saved.')));
              $Sender->render($this->getView('character-new.php'));
              break;
            }
            $Sender->SetData("success", sprintf(T('Character saved at %s.'), Gdn_Format::Date()));

//            Redirect('/discussions/characters/edit/' . $id);
          }

          $Sender->SetData('character', $character);
          $Sender->render($this->getView('character-new.php'));
          break;

          default:
            // this is a default character listing with an undefined endpoint.
            // Treat it as a search by game..
            $characters = $this->GetCharacters(["Game" => $args[0]]);
            $Sender->SetData('characters', $characters);
            $Sender->SetData('Game', $args[0]);
            $Sender->render($this->getView('characters.php'));
            break;
        }
    }

    public function RootController_RestGetCharacter_Handler($sender, $args)
    {
      $id = $args['id'];
      $args['character'] = $this->GetCharacter($id);
    }

    public function DiscussionsController_RestListCharacters_Handler($sender, $args)
    {
      $Session = Gdn::Session();
      $characters = $this->GetCharacters();
      foreach ( $characters as $character )
      {
        ?>
        <li class='Item ItemDiscussion CharacterListItem' id='character-<?php echo $character['CharacterId']; ?>'>
          <div class='character-meta-container'>
            <img class='character-thumb' style='background-image: url("<?php echo $character['ImgThumb']; ?>")' />
          </div>
          <div class="CharacterData">
            <h2><?php echo $character['Name']; ?> <small>[<?php echo $character['Slug'] . " in " . $character['Game']; ?>]</small></h2>
            <p class="description"><?php echo $character['SheetData']; ?></p>
          </div>
          <span class='character-menu'>
            <?php if ( $character['Sheet'] != '' ) : ?>
              <a class='button' href='<?php echo $character['Sheet']; ?>'><i class="fa fa-file-text-o" aria-hidden="true"></i> Sheet</a>
            <?php endif; ?>
            <?php if ( is_object($Session->User) && (($character['Owner'] == $Session->User->UserID) || ($Session->User->Admin == '1')) ) : ?>
              <a class='button character-edit' href='/discussions/characters/edit/<?php echo $character['CharacterId']; ?>'><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
              <a class='button character-delete' data-id='<?php echo $character['CharacterId']; ?>' data-slug='<?php echo $character['Slug']; ?>' href='#'><i class="fa fa-times" aria-hidden="true"></i> Delete</a>
            <?php endif; ?>
          </span>
        </li>
        <?php
      }
    }


    /**
     * Plugin setup
     *
     * This method is fired only once, immediately after the plugin has been enabled in the /plugins/ screen,
     * and is a great place to perform one-time setup tasks, such as database structure changes,
     * addition/modification of config file settings, filesystem changes, etc.
     */
    public function setup()
    {
        // LogMessage(__FILE__,__LINE__,'CharacterPlugin','setup',"Initializing the plugin");

        // Set up the plugin's default values
        // None (yet).

        // Trigger database changes
        $this->structure();
    }

    /**
     * This is a special method name that will automatically trigger when a forum owner runs /utility/update.
     * It must be manually triggered if you want it to run on Setup().
     */
    public function structure()
    {
        // Create table GDN_Example, if it doesn't already exist
        // LogMessage(__FILE__,__LINE__,'CharacterPlugin','structure',"Updating SQL Model");
        Gdn::Structure()
            ->Table('Characters')
            ->PrimaryKey('CharacterId')
            ->Column('Name', 'varchar(255)')
            ->Column('Slug', 'varchar(64)', FALSE, 'unique')
            ->Column('ImgBanner', 'varchar(128)', TRUE)
            ->Column('ImgThumb', 'varchar(128)')
            ->Column('SheetData', 'text', TRUE)
            ->Column('Sheet', 'varchar(128)', TRUE)
            ->Column('Owner', 'varchar(128)')
            ->Set();

          Gdn::Structure()
            ->Table('SheetTemplates')
            ->PrimaryKey('SheetId')
            ->Column('Name', 'varchar(255)')
            ->Column('SheetTemplate', 'text', TRUE)
            ->Column('ShortCode', 'text', TRUE)
            ->Set();
      }

    /**
     * Plugin cleanup
     *
     * This method is fired only once, immediately before the plugin is disabled, and is a great place to
     * perform cleanup tasks such as deletion of unsued files and folders.
     */
    public function onDisable() {
        // removeFromConfig('Plugin.Example.TrimSize');
        // removeFromConfig('Plugin.Example.RenderCondition');

        // Never delete from the database OnDisable.
        // Usually, you want re-enabling a plugin to be as if it was never off.
    }

}
