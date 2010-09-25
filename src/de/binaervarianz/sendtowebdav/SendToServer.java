package de.binaervarianz.sendtowebdav;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;

public class SendToServer extends Activity {

	
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        
        Intent intent = getIntent();
        Bundle extras = intent.getExtras();
        
        if(intent.getAction().equals( Intent.ACTION_SEND ) && extras != null) {
        	String url = "";
        	if (extras.containsKey(Intent.EXTRA_TEXT)) {
        		url += extras.getString(Intent.EXTRA_TEXT);
        	}
        		
        AlertDialog.Builder alertDialogBuilder = new  AlertDialog.Builder(this);
        alertDialogBuilder.setMessage(url.toString())
        		   .setCancelable(false)
        		   .setPositiveButton("Yes", new DialogInterface.OnClickListener() {
        			   public void onClick(DialogInterface dialog, int id) {
        				   dialog.cancel();
        			   }
        		   })
        .setNegativeButton("No", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int id) {
                 dialog.cancel();
            }
        });
        AlertDialog alertDialog = alertDialogBuilder.create();
        alertDialog.show();
        }
    }
}
